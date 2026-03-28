<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;

class ValidationController extends Controller
{
    public function validationIndex(Request $request)
    {
        // 1. Ambil Antrean Laporan Harian
        $pendingReports = DailyReport::with(['user.divisi', 'shift'])
            ->where('status', 'pending')
            ->latest('tanggal')
            ->get();

        // 2. Ambil Log Rating Pelanggan (50 data terbaru)
        $feedbacks = CustomerFeedback::with('user')
            ->latest('created_at')
            ->limit(50)
            ->get();

        // 3. Ambil Log Nilai Asesmen Kuis (50 data terbaru)
        $assessments = TechnicalAssessment::with('user')
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('manager.validation', compact('pendingReports', 'feedbacks', 'assessments'));
    }

    public function validationShow($id)
    {
        try {
            // PERUBAHAN: Tambahkan 'shift' ke dalam eager loading
            $report = DailyReport::with(['user', 'details.variabelKpi', 'shift'])->findOrFail($id);

            $cases = $report->details->where('tipe_kegiatan', 'case');
            $activities = $report->details->where('tipe_kegiatan', 'activity');

            // Kita buat file blade terpisah khusus untuk isi dari accordion
            return view('manager.partials.validation-detail', compact('report', 'cases', 'activities'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function validationStore(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:daily_reports,id',
            'status'    => 'required|in:approved,rejected',
            'catatan_manager' => 'nullable|string'
        ]);

        $report = DailyReport::with(['user', 'details', 'shift'])->findOrFail($request->report_id);

        $report->update([
            'status'          => $request->status,
            'catatan_manager' => $request->catatan_manager,
            'validated_at'    => now(),
        ]);

        if ($request->status === 'approved') {
            $this->sendTelegramNotification($report);
        }

        return redirect()->route('manager.approval.index')->with('success', 'Status laporan berhasil diperbarui.');
    }

    private function sendTelegramNotification($report)
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        $namaDivisi = $report->user->divisi->nama_divisi;

        $message = "*BERIKUT LAPORAN ACTIVITY PADA HARI INI*\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n";
        $message .= "👤 *Nama:* " . $report->user->nama_lengkap . "\n";
        $message .= "🏢 *Divisi:* " . $namaDivisi . "\n";

        // PERUBAHAN: Tambahan info Shift di Telegram jika TAC
        if ($report->shift) {
            $message .= "⏰ *Shift:* " . $report->shift->nama_shift . "\n";
        }

        // Pastikan locale diatur ke Indonesia (bisa ditaruh di AppServiceProvider atau langsung di sini)
        \Carbon\Carbon::setLocale('id');
        // Tambahkan timezone('Asia/Jakarta') sebelum translatedFormat
        $message .= "📅 *Tanggal:* " . $report->tanggal->timezone('Asia/Jakarta')->translatedFormat('d F Y') . "\n";
        $message .= "🕒 *Disubmit pada:* " . $report->created_at->timezone('Asia/Jakarta')->translatedFormat('l, d F Y \p\u\k\u\l H:i') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n\n";

        if (str_contains(strtolower($namaDivisi), 'infrastructure') || str_contains(strtolower($namaDivisi), 'infra')) {
            $mainWorks = $report->details->whereNotIn('kategori', ['Lainnya'])->whereNotNull('kategori');
            $otherWorks = $report->details->whereIn('kategori', ['Lainnya']);

            $counter = 1;

            if ($mainWorks->isNotEmpty()) {
                $message .= "📋 *Technical Activities:*\n";
                foreach ($mainWorks as $detail) {
                    $label = "*{$detail->kategori}*: ";
                    $message .= "{$counter}. {$label}{$detail->deskripsi_kegiatan}\n";
                    $counter++;
                }
            }

            if ($mainWorks->isNotEmpty() && $otherWorks->isNotEmpty()) {
                $message .= "\n";
            }

            if ($otherWorks->isNotEmpty()) {
                $message .= "📂 *General Activities:*\n";
                foreach ($otherWorks as $detail) {
                    $message .= "{$counter}. {$detail->deskripsi_kegiatan}\n";
                    $counter++;
                }
            }
        } else {
            $cases = $report->details->where('tipe_kegiatan', 'case');
            if ($cases->isNotEmpty()) {
                $message .= "🛠 *Technical Activities:*\n";
                $i = 1;
                foreach ($cases as $case) {
                    if ($case->kategori === 'GPS') {
                        // Cek jika value_raw adalah 'ALL' atau '0', tampilkan sesuai kebutuhan
                        $kendaraan = ($case->value_raw == '0' || $case->value_raw == 'ALL') ? 'ALL' : $case->value_raw;
                        $message .= "{$i}. {$case->deskripsi_kegiatan} ({$kendaraan} Kendaraan)\n";
                    } else {
                        // Logika baru: Jika nomor_tiket ada, buat format string-nya. Jika tidak, kosongkan.
                        $tiketInfo = $case->nomor_tiket ? " (Nomor Tiket: {$case->nomor_tiket})" : "";

                        $message .= "{$i}. {$case->deskripsi_kegiatan}{$tiketInfo}\n";
                    }
                    $i++;
                }
                $message .= "\n";
            }

            $activities = $report->details->where('tipe_kegiatan', 'activity');
            if ($activities->isNotEmpty()) {
                $message .= "📝 *General Activities:*\n";
                $j = 1;
                foreach ($activities as $act) {
                    $message .= "{$j}. {$act->deskripsi_kegiatan}\n";
                    $j++;
                }
            }
        }

        if ($report->catatan_manager) {
            $message .= "\n💬 *Manager Note:* _" . $report->catatan_manager . "_\n";
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━\n";
        $message .= "Keep up the good work! 🚀";

        try {
            Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            \Log::error("Telegram Notification Failed: " . $e->getMessage());
        }
    }
}
