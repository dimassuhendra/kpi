<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\DailyReport;
use App\Models\User;


class ValidationController extends Controller
{
    public function validationIndex(Request $request)
    {
        $pendingReports = DailyReport::with('user')
            ->where('status', 'pending')
            ->latest('tanggal')
            ->get();

        return view('manager.validation', compact('pendingReports'));
    }

    public function validationShow($id)
    {
        try {
            $report = DailyReport::with(['user', 'details.variabelKpi'])->findOrFail($id);

            $cases = $report->details->where('tipe_kegiatan', 'case');
            $activities = $report->details->where('tipe_kegiatan', 'activity');

            return view('manager.partials.validation-detail', compact('report', 'cases', 'activities'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function validationUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan_manager' => 'nullable|string' // Opsional: alasan reject
        ]);

        $report = DailyReport::findOrFail($id);
        $report->update([
            'status' => $request->status,
            'keterangan_manager' => $request->keterangan_manager,
            'validated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function validationStore(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:daily_reports,id',
            'status'    => 'required|in:approved,rejected',
            'catatan'   => 'nullable|string'
        ]);

        $report = DailyReport::with(['user', 'details'])->findOrFail($request->report_id);

        $report->update([
            'status'           => $request->status,
            'catatan_manager'  => $request->catatan,
        ]);

        // LOGIKA NOTIFIKASI TELEGRAM
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
        $message .= "📅 *Tanggal:* " . $report->tanggal->format('d M Y') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n\n";

        // LOGIKA PER DIVISI
        if (str_contains(strtolower($namaDivisi), 'infrastructure') || str_contains(strtolower($namaDivisi), 'infra')) {
            /** * FORMAT INFRASTRUCTURE */

            // 1. Filter data
            $mainWorks = $report->details->whereNotIn('kategori', ['Lainnya'])->whereNotNull('kategori');
            $otherWorks = $report->details->whereIn('kategori', ['Lainnya']);

            $counter = 1;

            // 2. Tampilkan Kategori Utama (Network, GPS, CCTV)
            if ($mainWorks->isNotEmpty()) {
                $message .= "📋 *Technical Activities:*\n";
                foreach ($mainWorks as $detail) {
                    $label = "*{$detail->kategori}*: ";
                    $message .= "{$counter}. {$label}{$detail->deskripsi_kegiatan}\n";
                    $counter++;
                }
            }

            // Tambahkan baris baru sebagai pemisah jika kedua grup data ada
            if ($mainWorks->isNotEmpty() && $otherWorks->isNotEmpty()) {
                $message .= "\n";
            }

            // 3. Tampilkan Kategori Lainnya
            if ($otherWorks->isNotEmpty()) {
                $message .= "📂 *General Activities:*\n";
                foreach ($otherWorks as $detail) {
                    $message .= "{$counter}. {$detail->deskripsi_kegiatan}\n";
                    $counter++;
                }
            }
        } else {
            /** * FORMAT TAC / UMUM 
             * Memisahkan Technical Cases & General Activities
             */

            // 1. Technical Cases
            $cases = $report->details->where('tipe_kegiatan', 'case');
            if ($cases->isNotEmpty()) {
                $message .= "🛠 *Technical Activities:*\n";
                $i = 1;
                foreach ($cases as $case) {
                    // Cek apakah data ini adalah kategori GPS
                    if ($case->kategori === 'GPS') {
                        // Jika GPS, tampilkan: Judul (Angka/ALL)
                        $message .= "{$i}. {$case->deskripsi_kegiatan} ({$case->value_raw})\n";
                    } else {
                        // Jika Network (TAC), tampilkan judul saja sesuai permintaan
                        $message .= "{$i}. {$case->deskripsi_kegiatan}\n";
                    }
                    $i++;
                }
                $message .= "\n";
            }

            // 2. General Activities
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

        // Catatan Manager
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
