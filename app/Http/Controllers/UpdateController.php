<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    public function index()
    {
        $updates = [
            [
                'version' => '1.3.26',
                'date' => '26 Maret 2026',
                'title' => 'Laporan Excel 4 Tab & Dashboard Analitik Baru',
                'description' => 'Peningkatan besar pada sistem pelaporan Excel, tampilan dashboard manager yang lebih informatif, dan kemudahan edit data bagi staff.',
                'changes' => [
                    'Manager: Laporan Excel kini otomatis terbagi dalam 4 Tab (Ringkasan, Detail Kesalahan, Log Foto, & Kuis).',
                    'Manager: Dashboard baru dengan grafik warna (Hijau/Kuning/Merah) untuk memantau kecepatan respon staff secara real-time.',
                    'Manager: Filter data (Staff & Tanggal) kini bekerja instan tanpa perlu reload halaman (AJAX).',
                    'Manager: Tampilan validasi otomatis menyesuaikan kebutuhan data antara divisi TAC dan Infra.',
                    'Staff  : Halaman Log Aktivitas baru yang lebih detail, lengkap dengan fitur intip foto (Image Preview).',
                    'Staff  : Kini bisa mengedit seluruh isi laporan (Revisi) termasuk mengganti bukti foto dan jumlah kendaraan.',
                    'System : Perbaikan perhitungan waktu laporan agar lebih akurat, terutama untuk staff shift malam (GMT+7).'
                ],
                'type' => 'Major'
            ],
            [
                'version' => '1.3.7',
                'date' => '07 Maret 2026',
                'title' => 'Alur Revisi Laporan & Optimasi Dashboard',
                'description' => 'Peningkatan sistem validasi laporan untuk memudahkan koordinasi antara Staff dan Manager, serta pembaharuan navigasi dashboard.',
                'changes' => [
                    'Manager: Card "Pending Approval" kini interaktif dan bisa diklik langsung menuju halaman validasi.',
                    'Staff  : Kemampuan mengedit kembali laporan yang ditolak (rejected) oleh manager.',
                    'Staff  : Status laporan kini otomatis kembali ke "pending" setelah selesai direvisi.',
                    'Staff  : Penambahan informasi alasan penolakan dari Manager langsung pada dashboard staff.',
                    'System : Perbaikan responsivitas pada Widget Hero di berbagai ukuran layar.',
                    'System : Penambahan fitur hitung mundur (countdown) menyambut hari raya Lebaran.'
                ],
                'type' => 'Minor'
            ],
            [
                'version' => '1.3.1',
                'date' => '01 Maret 2026',
                'title' => 'Laporan Teknis & Pantauan GPS Baru',
                'description' => 'Pembaruan cara melapor kegiatan teknik dan pemantauan kendaraan GPS agar lebih detail dan rapi.',
                'changes' => [
                    'Manager: Urutan daftar nama pengguna kini lebih rapi (GM > Manager > Staff).',
                    'Manager: Badge ketika akan melakukan validasi laporan kini lebih informatif dan jelas.',
                    'Staff  : Laporan Monitoring (Jaringan & GPS) kini otomatis masuk ke kategori khusus Technical TAC.',
                    'Staff  : Menu baru untuk lapor activity GPS dengan form yang sudah disesuaikan.',
                    'Staff  : Tampilan kegiatan umum lebih bersih dan perbaikan error baris kosong saat mengetik laporan.'
                ],
                'type' => 'Major'
            ],
            [
                'version' => '1.2.24',
                'date' => '24 Februari 2026',
                'title' => 'Ramadhan Theme & Prayer Times',
                'description' => 'Pembaruan visual bertema Ramadhan dan integrasi jadwal sholat otomatis.',
                'changes' => [
                    'Implementasi tema Emerald & Gold di seluruh dashboard.',
                    'Fitur jadwal sholat dinamis (Imsak - Isya) untuk wilayah Bandar Lampung.',
                    'Efek animasi Star Dust pada interaksi mouse.',
                    'Menu "Pembaruan" baru dengan indikator notifikasi otomatis.'
                ],
                'type' => 'Major'
            ],
            [
                'version' => '1.2.21',
                'date' => '21 Februari 2026',
                'title' => 'Division Expansion & Role Optimization',
                'description' => 'Perluasan cakupan sistem untuk mendukung operasional administrasi perusahaan.',
                'changes' => [
                    'Integrasi Divisi Backoffice ke dalam sistem manajemen staff.',
                    'Pembaruan skema Role Staff untuk mendukung aksesibilitas administratif.',
                    'Penyesuaian dashboard untuk menampilkan data operasional Backoffice.',
                    'Noted: Grafik dashboard backoffice akan dibuat di pembaruan berikutnya setelah ada informasi variabel penilaian pada divisi backoffice.'
                ],
                'type' => 'Patch'
            ]
        ];

        $layout = (Auth::user()->role == 'manager' || Auth::user()->role == 'gm')
            ? 'layouts.manager'
            : 'layouts.staff';

        return view('updates', compact('updates', 'layout'));
    }
}
