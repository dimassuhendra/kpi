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
        $hariIni = now()->toDateString();

        // 1. Validasi awal: Memastikan ada input yang dikirim
        if ($user->divisi_id == 1) { // Logika TAC
            if (!$request->has('case') && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan (Case atau Activity).');
            }
        } else { // Logika Infra
            if (!$request->has('infra_activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan infrastruktur.');
            }
        }

        // 2. LOGIKA UTAMA: Cari laporan yang sudah ada atau buat baru
        // Kita filter berdasarkan user_id, tanggal hari ini, dan status pending.
        $report = DailyReport::firstOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'status'  => 'pending',
            ]
        );

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
                    // Lewati jika deskripsi kosong
                    if (empty($item['deskripsi'])) continue;

                    $isTemuan = isset($item['temuan_sendiri']);

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
                    if (empty($act['deskripsi'])) continue;

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
        else if ($user->divisi_id == 2) {
            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            if ($request->has('infra_activity')) {
                foreach ($request->infra_activity as $infra) {
                    // Lewati jika nama kegiatan kosong
                    if (empty($infra['nama_kegiatan'])) continue;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'kategori'           => $infra['kategori'],
                        'deskripsi_kegiatan' => $infra['nama_kegiatan'] . ': ' . $infra['deskripsi'],
                        'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                    ]);
                }
            }
        }

        return redirect()->route('staff.input')->with('success', 'Laporan berhasil diperbarui!');
    }
}
