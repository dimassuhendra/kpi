<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, $response)
    {
        // 1. Abaikan request untuk assets statis
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/i', $request->path())) {
            return;
        }

        // 2. Abaikan route keep-alive agar bot tidak melakukan spam otomatis
        if ($request->routeIs('keep-alive')) {
            return;
        }

        $user = Auth::check() ? Auth::user()->nama_lengkap . ' (ID: ' . Auth::user()->id . ')' : 'Guest / Belum Login';

        $method = $request->method();
        $ip = $request->header('X-Forwarded-For')
            ? trim(explode(',', $request->header('X-Forwarded-For'))[0])
            : $request->ip();
            
        // 3. Terjemahkan Method agar mudah dipahami orang awam
        $tindakan = match ($method) {
            'GET' => 'Membuka halaman',
            'POST' => 'Mengirim form',
            'PUT', 'PATCH' => 'Memperbarui data',
            'DELETE' => 'Menghapus data',
            default => $method
        };

        // 4. Terjemahkan URL berdasarkan Nama Route Laravel
        $routeName = $request->route() ? $request->route()->getName() : null;
        $aktivitas = $this->terjemahkanRoute($routeName, $request->path());

        // 5. Susun Pesan (Minim Simbol agar bersih)
        $message = "User: {$user}\n";
        $message .= "Aksi: {$tindakan}\n";
        $message .= "Aktivitas: {$aktivitas}\n";
        $message .= "IP Address: {$ip}\n";

        // 6. Tangkap payload hanya jika ada data yang dikirim (selain GET)
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $payload = $request->except(['password', 'password_confirmation', '_token']);

            // Hanya tampilkan payload jika memang ada isinya
            if (!empty($payload)) {
                $dataString = json_encode($payload, JSON_PRETTY_PRINT);

                if (strlen($dataString) > 500) {
                    $dataString = substr($dataString, 0, 500) . "\n... [teks dipotong]";
                }

                $message .= "\nData yang dikirim:\n```json\n{$dataString}\n```";
            }
        }
        $this->sendToTelegram($message);
    }

    /**
     * Fungsi untuk mengubah Nama Route menjadi kalimat yang mudah dibaca
     */
    private function terjemahkanRoute($routeName, $path)
    {
        // Jika route tidak memiliki nama, kembalikan path aslinya
        if (!$routeName) return "Path URL: /" . $path;

        // Kamus Terjemahan Halaman
        $kamus = [
            // Auth & Umum
            'login' => 'Halaman Login',
            'logout' => 'Melakukan Logout',
            'updates.index' => 'Halaman Pembaruan Sistem',

            // Staff
            'staff.dashboard' => 'Dashboard Staff (Main Station)',
            'staff.input' => 'Halaman Input Case',
            'staff.upload.async' => 'Upload File (Async)',
            'staff.kpi.store' => 'Submit / Simpan Form KPI',
            'staff.feedback.store' => 'Mengirim Feedback Staff',
            'staff.assessment.store' => 'Menyimpan Assessment Staff',
            'staff.kpi.logs' => 'Halaman Logs KPI',
            'staff.kpi.update' => 'Mengubah Data Log KPI',
            'staff.kpi.destroy' => 'Menghapus Data Log KPI',
            'staff.kpi.case-destroy' => 'Menghapus Case KPI',
            'staff.kpi.case_update' => 'Mengubah Case KPI',
            'staff.logs.export.excel' => 'Download Laporan Excel (Staff)',
            'staff.kpi.achievements' => 'Halaman Stats / Achievements',
            'staff.profile.edit' => 'Halaman Edit Profile Staff',
            'staff.profile.update' => 'Menyimpan Pembaruan Profile',

            // Manager & GM
            'manager.dashboard' => 'Dashboard Manager',
            'manager.approval.index' => 'Halaman Validation / Persetujuan',
            'manager.approval.show' => 'Melihat Detail Validasi',
            'manager.validation.update' => 'Mengubah Status Validasi',
            'manager.approval.store' => 'Menyimpan Data Validasi',
            'manager.assessment.store' => 'Menyimpan Assessment Manager',
            'manager.reports.index' => 'Halaman Archive Reports',
            'manager.reports.preview' => 'Melihat Preview Report',
            'manager.reports.export' => 'Download Report Bulanan',
            'manager.reports.export.divisi' => 'Download Report per Divisi',
            'manager.export.pdf' => 'Download Laporan PDF',
            'manager.reports.destroy' => 'Menghapus Range Laporan',
            'manager.users.index' => 'Halaman Kelola Data Users',
            'manager.users.store' => 'Menambahkan User Baru',
            'manager.users.update' => 'Memperbarui Data User',
            'manager.users.destroy' => 'Menghapus Data User',
            'manager.export.all' => 'Download Data Semua Staff',
            'manager.profile.index' => 'Halaman Profile Manager',
            'manager.profile.update' => 'Menyimpan Pembaruan Profile Manager',
        ];

        // Cek apakah route ada di kamus, jika tidak ada, rapikan nama route aslinya
        return $kamus[$routeName] ?? "Aktivitas pada: " . str_replace(['.', '_', '-'], ' ', title_case($routeName));
    }

    private function sendToTelegram($message)
    {
        $token = env('TELEGRAM_ADMIN_BOT_TOKEN');
        $chatId = env('TELEGRAM_ADMIN_CHAT_ID');

        if (!$token || !$chatId) return;

        try {
            Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            // Abaikan error agar web tetap berjalan lancar
        }
    }
}
