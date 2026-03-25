<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\VariabelKpi;
use Carbon\Carbon;

class DummyKpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil SEMUA User TAC dan INFRA menggunakan get()
        $usersTac = User::where('divisi_id', 1)->get();
        $usersInfra = User::where('divisi_id', 2)->get();

        // 2. Ambil Variabel KPI ID (Jika ada)
        $varTac = VariabelKpi::where('divisi_id', 1)->where('nama_variabel', 'Jumlah Case Harian')->first();
        $varInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

        // Looping untuk bulan Maret (tanggal 1 - 31)
        for ($day = 1; $day <= 31; $day++) {
            // Setup tanggal spesifik: 2026-03-XX
            $date = Carbon::create(2026, 3, $day, 0, 0, 0, 'Asia/Jakarta')->toDateString();

            // ==========================================
            // A. DATA DUMMY UNTUK SELURUH USER TAC
            // ==========================================
            foreach ($usersTac as $userTac) {
                $reportTac = DailyReport::create([
                    'user_id' => $userTac->id,
                    'tanggal' => $date,
                    'is_gps_ontime' => 1,
                    'is_dashboard_ontime' => 1,
                    'status' => 'approved', // Sesuai permintaan (Approval Only)
                    'shift_id' => rand(1, 3), // Diacak shift-nya (asumsi ID shift 1, 2, atau 3)
                ]);

                // TAC: 1. Monitoring Network (Default Setup)
                KegiatanDetail::create([
                    'daily_report_id' => $reportTac->id,
                    'tipe_kegiatan' => 'case',
                    'kategori' => 'Network',
                    'deskripsi_kegiatan' => 'Monitoring Network',
                    'temuan_sendiri' => 0,
                    'is_mandiri' => 1,
                ]);

                // TAC: 2. Random Case Network
                $isTemuan = rand(0, 1);
                KegiatanDetail::create([
                    'daily_report_id' => $reportTac->id,
                    'variabel_kpi_id' => $varTac ? $varTac->id : null,
                    'tipe_kegiatan' => 'case',
                    'kategori' => 'Network',
                    'deskripsi_kegiatan' => 'Troubleshoot Link Down (Dummy)',
                    'nomor_tiket' => 'INC-' . rand(1000, 9999),
                    'temuan_sendiri' => $isTemuan,
                    'is_mandiri' => $isTemuan === 0 ? rand(0, 1) : 1,
                    'waktu_respon_menit' => $isTemuan === 1 ? 0 : [5, 10, 15, 20][array_rand([5, 10, 15, 20])],
                ]);

                // TAC: 3. Monitoring GPS (Default Setup)
                KegiatanDetail::create([
                    'daily_report_id' => $reportTac->id,
                    'tipe_kegiatan' => 'case',
                    'kategori' => 'GPS',
                    'deskripsi_kegiatan' => 'Monitoring GPS',
                    'value_raw' => 'ALL',
                    'temuan_sendiri' => 0,
                    'is_mandiri' => 1,
                ]);
            }

            // ==========================================
            // B. DATA DUMMY UNTUK SELURUH USER INFRA
            // ==========================================
            foreach ($usersInfra as $userInfra) {
                $reportInfra = DailyReport::create([
                    'user_id' => $userInfra->id,
                    'tanggal' => $date,
                    'is_gps_ontime' => 0,
                    'is_dashboard_ontime' => 1,
                    'status' => 'approved', // Sesuai permintaan (Approval Only)
                    'shift_id' => 1, // Infra biasanya shift pagi (sesuaikan jika perlu)
                ]);

                // Array randomisasi kategori dan deskripsi untuk Infra
                $categories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
                $descs = [
                    'Network' => ['Pemasangan Router Baru', 'Maintenance Kabel FO', 'Setup Switch Gedung', 'Pengecekan Jaringan LAN'],
                    'CCTV' => ['Instalasi CCTV', 'Perbaikan DVR Mati', 'Pengecekan Sudut Kamera', 'Maintenance Server CCTV'],
                    'GPS' => ['Pemasangan GPS Tracker Unit A', 'Perbaikan GPS tidak kirim data', 'Kalibrasi Sensor GPS'],
                    'Lainnya' => ['Pembersihan Rak Server', 'Inventarisasi Perangkat Baru', 'Meeting Koordinasi Vendor']
                ];

                // Masing-masing user Infra akan mendapat 1 sampai 3 kegiatan random per hari
                $numInfraActivities = rand(1, 3);

                for ($i = 0; $i < $numInfraActivities; $i++) {
                    $kategori = $categories[array_rand($categories)];
                    // Sesuai controller: Jika lainnya maka activity, sisanya case
                    $tipe = ($kategori == 'Lainnya') ? 'activity' : 'case';
                    $desc = $descs[$kategori][array_rand($descs[$kategori])];

                    KegiatanDetail::create([
                        'daily_report_id' => $reportInfra->id,
                        'variabel_kpi_id' => $varInfra ? $varInfra->id : null,
                        'tipe_kegiatan' => $tipe,
                        'kategori' => $kategori,
                        'deskripsi_kegiatan' => $desc,
                        'is_mandiri' => 1,
                    ]);
                }
            }
        }
    }
}
