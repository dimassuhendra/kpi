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

        // --- TAMBAHAN: Ambil Daftar User Khusus Divisi TAC untuk Form Input Kuis ---
        $tacUsers = User::whereHas('divisi', function ($q) {
            $q->where('nama_divisi', 'like', '%TAC%');
        })->orderBy('nama_lengkap', 'asc')->get();

        return view('manager.validation', compact(
            'pendingReports',
            'feedbacks',
            'assessments',
            'tacUsers'
        ));
    }

    public function storeAssessment(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000',
            'jumlah_soal'   => 'required|integer|min:1',
            'jumlah_benar'  => 'required|integer|min:0|lte:jumlah_soal',
            'bukti_kuis'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $path = null;
            if ($request->hasFile('bukti_kuis')) {
                $path = $request->file('bukti_kuis')->store('bukti_kuis', 'public');
            }

            TechnicalAssessment::create([
                'user_id'       => $request->user_id,
                'periode_bulan' => $request->periode_bulan,
                'periode_tahun' => $request->periode_tahun,
                'jumlah_soal'   => $request->jumlah_soal,
                'jumlah_benar'  => $request->jumlah_benar,
                'bukti_kuis'    => $path,
            ]);

            return redirect()->back()->with('success', 'Data kuis berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function validationShow($id)
    {
        try {
            // PERUBAHAN: Tambahkan 'lemburReport' ke dalam eager loading
            $report = DailyReport::with(['user', 'details.variabelKpi', 'shift', 'lemburReport'])->findOrFail($id);

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
            'report_id'       => 'required|exists:daily_reports,id',
            'status'          => 'required|in:approved,rejected',
            'catatan_manager' => 'nullable|string'
        ]);

        $report = DailyReport::with(['user', 'details', 'shift', 'lemburReport'])->findOrFail($request->report_id);

        // 1. Update status laporan harian
        $report->update([
            'status'          => $request->status,
            'catatan_manager' => $request->catatan_manager,
            'validated_at'    => now(),
        ]);

        // 2. (Opsional) Jika tabel lembur_reports punya kolom status, kita selaraskan otomatis
        if ($report->lemburReport) {
            foreach ($report->lemburReport as $lembur) {
                // Hapus baris ini jika tabel lembur tidak memiliki kolom status
                $lembur->update(['status' => $request->status]);
            }
        }

        if ($request->status === 'approved') {
            $this->sendTelegramNotification($report);
        }

        return redirect()->route('manager.approval.index')->with('success', 'Laporan dan Lembur berhasil divalidasi.');
    }

    private function sendTelegramNotification($report)
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        $namaDivisi = $report->user->divisi->nama_divisi ?? 'General';
        \Carbon\Carbon::setLocale('id');

        // Header yang formal dan bersih
        $message = "*LAPORAN AKTIVITAS HARIAN*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

        // Identitas
        $message .= "*Nama:* " . $report->user->nama_lengkap . "\n";
        $message .= "*Divisi:* " . strtoupper($namaDivisi) . "\n";

        if ($report->shift) {
            $message .= "*Shift:* " . $report->shift->nama_shift . "\n";
        }

        $message .= "*Tanggal:* " . $report->tanggal->timezone('Asia/Jakarta')->translatedFormat('d F Y') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        $namaDivisiLower = strtolower($namaDivisi);
        $isInfra = str_contains($namaDivisiLower, 'infra');
        $isTac = str_contains($namaDivisiLower, 'tac');

        // ==========================================
        // 1. BLOK AKTIVITAS UTAMA (HARIAN)
        // ==========================================
        if ($isInfra) {
            // --- LOGIKA KHUSUS INFRASTRUCTURE ---
            $mainWorks = $report->details->whereNotIn('kategori', ['Lainnya'])->whereNotNull('kategori');
            $otherWorks = $report->details->whereIn('kategori', ['Lainnya']);

            if ($mainWorks->isNotEmpty()) {
                $message .= "*Technical Activities:*\n";
                $counter = 1;
                foreach ($mainWorks as $detail) {
                    $judul = $detail->nama_kegiatan ?? '';
                    $deskripsi = trim($detail->deskripsi_kegiatan, " :-");
                    $teksKegiatan = !empty($deskripsi) ? "{$judul} - {$deskripsi}" : $judul;

                    $message .= "{$counter}. *{$detail->kategori}:* {$teksKegiatan}\n";
                    $counter++;
                }
                $message .= "\n";
            }

            if ($otherWorks->isNotEmpty()) {
                $message .= "*General Activities:*\n";
                $counter = 1;
                foreach ($otherWorks as $detail) {
                    $judul = $detail->nama_kegiatan ?? '';
                    $deskripsi = trim($detail->deskripsi_kegiatan, " :-");
                    $teksKegiatan = (!empty($judul) && !empty($deskripsi)) ? "{$judul} - {$deskripsi}" : ($judul ?: $deskripsi);

                    $message .= "{$counter}. {$teksKegiatan}\n";
                    $counter++;
                }
                $message .= "\n";
            }
        } elseif ($isTac) {
            // --- LOGIKA KHUSUS TAC ---
            $cases = $report->details->where('tipe_kegiatan', 'case');
            if ($cases->isNotEmpty()) {
                $message .= "*Technical Activities:*\n";
                $i = 1;
                foreach ($cases as $case) {
                    $deskripsi = trim($case->deskripsi_kegiatan, " :-");

                    if ($case->kategori === 'GPS') {
                        $kendaraan = ($case->value_raw == '0' || strtoupper($case->value_raw) == 'ALL') ? 'ALL' : $case->value_raw;
                        $message .= "{$i}. {$deskripsi} \n    └ Total: {$kendaraan} Unit\n";
                    } else {
                        if ($case->nomor_tiket) {
                            $message .= "{$i}. {$deskripsi} \n    └ Tiket: {$case->nomor_tiket}\n";
                        } else {
                            $message .= "{$i}. {$deskripsi}\n";
                        }
                    }
                    $i++;
                }
                $message .= "\n";
            }

            $activities = $report->details->where('tipe_kegiatan', 'activity');
            if ($activities->isNotEmpty()) {
                $message .= "*General Activities:*\n";
                $j = 1;
                foreach ($activities as $act) {
                    $deskripsi = trim($act->deskripsi_kegiatan, " :-");
                    $message .= "{$j}. {$deskripsi}\n";
                    $j++;
                }
                $message .= "\n";
            }
        } else {
            // --- LOGIKA KHUSUS BOT, PURCHASING, BACKOFFICE ---
            $semuaAktivitas = $report->details;

            if ($semuaAktivitas->isNotEmpty()) {
                $message .= "*Activities:*\n";
                $counter = 1;
                foreach ($semuaAktivitas as $act) {
                    $deskripsi = trim($act->deskripsi_kegiatan, " :-");
                    $message .= "{$counter}. {$deskripsi}\n";
                    $counter++;
                }
                $message .= "\n";
            }
        }

        // ==========================================
        // 2. BLOK AKTIVITAS LEMBUR (JIKA ADA)
        // ==========================================
        if ($report->lemburReport && $report->lemburReport->isNotEmpty()) {
            $message .= "*AKTIVITAS LEMBUR:*\n";
            $k = 1;
            foreach ($report->lemburReport as $lembur) {
                $mulai = \Carbon\Carbon::parse($lembur->waktu_mulai)->timezone('Asia/Jakarta');
                $selesai = \Carbon\Carbon::parse($lembur->waktu_selesai)->timezone('Asia/Jakarta');

                $totalMenit = $mulai->diffInMinutes($selesai);
                $jam = floor($totalMenit / 60);
                $menit = $totalMenit % 60;

                $teksDurasi = '';
                if ($jam > 0) $teksDurasi .= "{$jam}j ";
                if ($menit > 0) $teksDurasi .= "{$menit}m";
                $teksDurasi = trim($teksDurasi) ?: '0m';

                $message .= "{$k}. {$lembur->detail_pekerjaan}\n";
                $message .= "    └ Durasi: {$mulai->format('H:i')} - {$selesai->format('H:i')} WIB ({$teksDurasi})\n";
                $k++;
            }
            $message .= "\n";
        }

        // ==========================================
        // 3. BLOK CATATAN MANAGER & PENUTUP
        // ==========================================
        if (!empty($report->catatan_manager)) {
            $message .= "*CATATAN MANAGER:*\n";
            $message .= "_\"{$report->catatan_manager}\"_\n\n";
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "_Disubmit pada: " . $report->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . " WIB_";

        try {
            \Illuminate\Support\Facades\Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Telegram Notification Failed: " . $e->getMessage());
        }
    }
}
