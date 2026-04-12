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

        $namaDivisi = $report->user->divisi->nama_divisi;

        $message = "*BERIKUT LAPORAN ACTIVITY PADA HARI INI*\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n";
        $message .= "*Nama:* " . $report->user->nama_lengkap . "\n";
        $message .= "*Divisi:* " . $namaDivisi . "\n";

        // Tambahan info Shift di Telegram jika TAC
        if ($report->shift) {
            $message .= "*Shift:* " . $report->shift->nama_shift . "\n";
        }

        // Pastikan locale diatur ke Indonesia
        \Carbon\Carbon::setLocale('id');
        $message .= "*Tanggal:* " . $report->tanggal->timezone('Asia/Jakarta')->translatedFormat('d F Y') . "\n";
        $message .= "*Disubmit pada:* " . $report->created_at->timezone('Asia/Jakarta')->translatedFormat('l, d F Y \p\u\k\u\l H:i') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n\n";

        // ==========================================
        // 1. BLOK AKTIVITAS UTAMA (HARIAN)
        // ==========================================
        if (str_contains(strtolower($namaDivisi), 'infrastructure') || str_contains(strtolower($namaDivisi), 'infra')) {
            $mainWorks = $report->details->whereNotIn('kategori', ['Lainnya'])->whereNotNull('kategori');
            $otherWorks = $report->details->whereIn('kategori', ['Lainnya']);

            $counter = 1;

            if ($mainWorks->isNotEmpty()) {
                $message .= "*Technical Activities:*\n";
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
                $message .= "*General Activities:*\n";
                foreach ($otherWorks as $detail) {
                    $message .= "{$counter}. {$detail->deskripsi_kegiatan}\n";
                    $counter++;
                }
            }
        } else {
            $cases = $report->details->where('tipe_kegiatan', 'case');
            if ($cases->isNotEmpty()) {
                $message .= "*Technical Activities:*\n";
                $i = 1;
                foreach ($cases as $case) {
                    if ($case->kategori === 'GPS') {
                        $kendaraan = ($case->value_raw == '0' || $case->value_raw == 'ALL') ? 'ALL' : $case->value_raw;
                        $message .= "{$i}. {$case->deskripsi_kegiatan} ({$kendaraan} Kendaraan)\n";
                    } else {
                        $tiketInfo = $case->nomor_tiket ? " (Nomor Tiket: {$case->nomor_tiket})" : "";
                        $message .= "{$i}. {$case->deskripsi_kegiatan}{$tiketInfo}\n";
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
                    $message .= "{$j}. {$act->deskripsi_kegiatan}\n";
                    $j++;
                }
            }
        }

        // ==========================================
        // 2. BLOK AKTIVITAS LEMBUR (JIKA ADA)
        // ==========================================
        if ($report->lemburReport && $report->lemburReport->isNotEmpty()) {
            $message .= "\n*Pekerjaan Lembur:*\n";
            $k = 1;
            foreach ($report->lemburReport as $lembur) {
                // Konversi string ke format waktu dan pastikan zona waktu sesuai
                $mulai = \Carbon\Carbon::parse($lembur->waktu_mulai)->timezone('Asia/Jakarta');
                $selesai = \Carbon\Carbon::parse($lembur->waktu_selesai)->timezone('Asia/Jakarta');

                // 1. Dapatkan total keseluruhan menit lembur
                $totalMenit = $mulai->diffInMinutes($selesai);

                // 2. Bagi 60 dan bulatkan ke bawah untuk dapat Jam bulat (contoh 48/60 = 0)
                $jam = floor($totalMenit / 60);

                // 3. Sisa baginya adalah Menit
                $menit = $totalMenit % 60;

                $teksDurasi = '';

                // Karena $jam sudah dibulatkan (pasti angka 0, 1, 2, dst)
                if ($jam > 0) {
                    $teksDurasi .= $jam . ' Jam ';
                }

                if ($menit > 0) {
                    $teksDurasi .= $menit . ' Menit';
                }

                // Menghapus spasi berlebih
                $teksDurasi = trim($teksDurasi);

                if ($teksDurasi == '') {
                    $teksDurasi = '0 Menit';
                }

                $message .= "{$k}. {$lembur->detail_pekerjaan}\n";
                // Tampilkan tanggal juga jaga-jaga jika lembur melewati tengah malam
                $message .= "   *Waktu Mulai:* " . $mulai->format('d F, H:i') . " WIB\n";
                $message .= "   *Waktu Selesai:* " . $selesai->format('d F, H:i') . " WIB\n";
                $message .= "   *Durasi:* " . trim($teksDurasi) . "\n";
                $k++;
            }
        }

        // ==========================================
        // 3. BLOK CATATAN MANAGER & PENUTUP
        // ==========================================
        if ($report->catatan_manager) {
            $message .= "\n*Manager Note:* _" . $report->catatan_manager . "_\n";
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
