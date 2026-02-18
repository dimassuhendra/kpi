<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\VariabelKpi;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;

class InputController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil variabel KPI sesuai divisi (untuk TAC biasanya Case, untuk Infra biasanya Kategori)
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Logika baris awal (formattedRows)
        if ($user->divisi_id == 2) {
            // Baris awal default untuk tim Infrastruktur
            $formattedRows = [[
                'nama_kegiatan' => '',
                'deskripsi' => '',
                'kategori' => 'Network',
            ]];
        } else {
            // Baris awal default untuk tim TAC
            $formattedRows = [[
                'deskripsi' => '',
                'respons' => '',
                'temuan_sendiri' => false,
                'is_mandiri' => 1,
                'pic_name' => ''
            ]];
        }

        // SEMUA DIVISI diarahkan ke file yang sama: staff/input_kpi.blade.php
        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi awal
        if ($user->divisi_id == 1) { // Logika TAC
            if (!$request->has('case') && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
            }
        } else { // Logika Infra
            if (!$request->has('infra_activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan infrastruktur.');
            }
        }

        // 2. Buat Header DailyReport
        $report = DailyReport::create([
            'user_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'status'  => 'pending',
        ]);

        // ---------------------------------------------------------
        // LOGIKA PROSES: DIVISI TAC (DIVISI ID: 1)
        // ---------------------------------------------------------
        if ($user->divisi_id == 1) {
            $variabelKpis = VariabelKpi::where('divisi_id', 1)->get();
            $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();
            $vRespons = $variabelKpis->where('nama_variabel', 'Durasi Response (Ambang Batas 15 Menit)')->first();
            $vTemuan = $variabelKpis->where('nama_variabel', 'Case Ditemukan Sendiri')->first();
            $vMandiri = $variabelKpis->where('nama_variabel', 'Penyelesaian Mandiri (Bonus)')->first();

            // Proses Case TAC
            if ($request->has('case')) {
                foreach ($request->case as $item) {
                    $poinCaseIni = 0;
                    if ($vCount) $poinCaseIni += $vCount->bobot;

                    $isTemuan = isset($item['temuan_sendiri']);
                    if ($isTemuan) {
                        if ($vTemuan) $poinCaseIni += $vTemuan->bobot;
                    } else {
                        if ($vRespons) {
                            $poinCaseIni += (($item['respons'] ?? 0) <= 15) ? $vRespons->bobot : ($vRespons->bobot * 0.5);
                        }
                    }

                    if (($item['is_mandiri'] ?? '1') == '1') {
                        if ($vMandiri) $poinCaseIni += $vMandiri->bobot;
                    }

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => $vCount ? $vCount->id : null,
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'value_raw'          => $item['respons'] ?? 0,
                        'temuan_sendiri'     => $isTemuan ? 1 : 0,
                        'is_mandiri'         => $item['is_mandiri'] ?? 1,
                        'pic_name'           => ($item['is_mandiri'] ?? '1') == '0' ? ($item['pic_name'] ?? '') : null,
                    ]);
                }
            }

            // Proses General Activity TAC
            if ($request->has('activity')) {
                foreach ($request->activity as $act) {
                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'deskripsi_kegiatan' => $act['deskripsi'],
                    ]);
                }
            }
        }

        // ---------------------------------------------------------
        // LOGIKA PROSES: DIVISI INFRASTRUKTUR (DIVISI ID: 2)
        // ---------------------------------------------------------
        // DI DALAM FUNCTION store()
        else if ($user->divisi_id == 2) {
            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            foreach ($request->infra_activity as $infra) {
                KegiatanDetail::create([
                    'daily_report_id'    => $report->id,
                    'tipe_kegiatan'      => 'activity',

                    // 1. INI YANG PALING PENTING: Simpan kategorinya ke kolom kategori
                    'kategori'           => $infra['kategori'],

                    // 2. Deskripsi biar rapi, tidak perlu pakai kurung siku lagi kalau sudah ada kolomnya
                    'deskripsi_kegiatan' => $infra['nama_kegiatan'] . ': ' . $infra['deskripsi'],

                    'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                ]);
            }
        }

        return redirect()->route('staff.input')->with('success', 'Laporan Berhasil Disimpan!');
    }
}
