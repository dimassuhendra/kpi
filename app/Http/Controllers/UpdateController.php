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
                'version' => '1.3.1',
                'date' => '01 Maret 2026',
                'title' => 'Technical Reporting & GPS Integration',
                'description' => 'Peningkatan struktur pelaporan teknis, transparansi PIC, dan integrasi modul pemantauan GPS.',
                'changes' => [
                    'Optimasi hierarki daftar pengguna berdasarkan jabatan (GM, Manager, Staff).',
                    'Pemisahan kategori presisi: Monitoring (Network & GPS) masuk ke Technical Activities.',
                    'Modul Input GPS baru dengan form kendala dan jumlah kendaraan.',
                    'Sistem klasifikasi penyelesaian tugas: Mandiri (Solo) vs Bantuan (Assist) dengan nama PIC.',
                    'Otomatisasi pencatatan monitoring GPS "ALL" untuk pemantauan seluruh kendaraan.',
                    'Visualisasi Badge Manager baru: Ikon Mobil (GPS), Jam (Response Time), dan Jabat Tangan (Assist).',
                    'Penyederhanaan tampilan General Activities dan pembersihan bug baris kosong pada laporan.'
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
