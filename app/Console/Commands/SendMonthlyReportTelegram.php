<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Exports\KpiExport;
use Maatwebsite\Excel\Facades\Excel;

class SendMonthlyReportTelegram extends Command
{
    protected $signature = 'report:send-monthly';
    protected $description = 'Mengirim laporan KPI bulanan per user ke Telegram';

    public function handle()
    {
        // Simulasi waktu (opsional, hapus jika sudah live)
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 3, 29, 5, 25, 0, 'Asia/Jakarta'));

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        $lastMonth = Carbon::now()->subMonth();
        $bulanNama = $lastMonth->translatedFormat('F Y');

        $users = User::with('divisi')->whereHas('divisi')->get();

        foreach ($users as $user) {
            $fileName = "KPI_Report_{$user->nama_lengkap}_{$lastMonth->format('Y-m')}.xlsx";
            $filePath = "reports/{$fileName}";

            // --- PERBAIKAN DI SINI ---
            // Bungkus semua data ke dalam satu array $filters agar cocok dengan constructor KpiExport
            $filters = [
                'user_id'    => $user->id,
                'start_date' => $lastMonth->copy()->startOfMonth()->format('Y-m-d'),
                'end_date'   => $lastMonth->copy()->endOfMonth()->format('Y-m-d'),
                // Tambahkan key lain jika memang dibutuhkan oleh sheet Anda
            ];

            // Kirim $filters sebagai satu-satunya parameter
            Excel::store(new KpiExport($filters), $filePath, 'public');
            // -------------------------

            try {
                Http::withoutVerifying()->attach(
                    'document',
                    Storage::disk('public')->get($filePath),
                    $fileName
                )->post("https://api.telegram.org/bot{$token}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => "📊 *REKAP LAPORAN BULANAN*\n" .
                        "━━━━━━━━━━━━━━━━━━\n" .
                        "👤 *Nama:* {$user->nama_lengkap}\n" .
                        "🏢 *Divisi:* {$user->divisi->nama_divisi}\n" .
                        "📅 *Periode:* {$bulanNama}\n" .
                        "━━━━━━━━━━━━━━━━━━\n" .
                        "Laporan terlampir dalam format Excel.",
                    'parse_mode' => 'Markdown',
                ]);

                Storage::disk('public')->delete($filePath);
                $this->info("Berhasil mengirim laporan: {$user->nama_lengkap}");
            } catch (\Exception $e) {
                \Log::error("Gagal kirim report bulanan untuk {$user->nama_lengkap}: " . $e->getMessage());
            }
        }

        \Carbon\Carbon::setTestNow(); // Reset simulasi waktu
    }
}
