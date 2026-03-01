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
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        if ($user->divisi_id == 1) { // TAC
            $formattedRows = [
                'network' => [
                    // Row Monitoring Default
                    [
                        'deskripsi' => 'Monitoring Network',
                        'is_monitoring' => true,
                        'is_default' => true
                    ],
                    // Row Input Case Kosong
                    [
                        'deskripsi' => '',
                        'respons' => '',
                        'temuan_sendiri' => false,
                        'is_mandiri' => 1,
                        'pic_name' => '',
                        'is_monitoring' => false,
                        'is_default' => false
                    ]
                ],
                'gps' => [
                    // Row Monitoring GPS Default
                    [
                        'nama_kegiatan' => 'Monitoring GPS',
                        'jumlah_kendaraan' => '',
                        'is_monitoring' => true,
                        'is_default' => true
                    ],
                    // Row Input GPS Kosong
                    [
                        'nama_kegiatan' => '',
                        'jumlah_kendaraan' => '',
                        'is_monitoring' => false,
                        'is_default' => false
                    ]
                ]
            ];
        } elseif ($user->divisi_id == 2) { // INFRA
            $formattedRows = [['nama_kegiatan' => '', 'deskripsi' => '', 'kategori' => 'Network']];
        } else { // BACKOFFICE
            $formattedRows = [['judul' => '', 'deskripsi' => '']];
        }

        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $hariIni = now()->toDateString();

        // 1. Validasi awal berdasarkan Divisi
        if ($user->divisi_id == 1) {
            if (!$request->has('case_network') && !$request->has('case_gps') && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
            }
        } elseif ($user->divisi_id == 2) {
            if (!$request->has('infra_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        } else {
            if (!$request->has('bo_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        }

        // 2. Buat atau cari Daily Report (Status Pending)
        $report = DailyReport::firstOrCreate([
            'user_id' => $user->id,
            'tanggal' => $hariIni,
            'status'  => 'pending',
        ]);

        // ---------------------------------------------------------
        // PROSES DIVISI TAC (ID: 1)
        // ---------------------------------------------------------
        if ($user->divisi_id == 1) {
            $variabelKpis = VariabelKpi::where('divisi_id', 1)->get();
            $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();

            // A. Proses Case Network (Sekarang SEMUA masuk tipe 'case')
            if ($request->has('case_network')) {
                foreach ($request->case_network as $item) {
                    if (empty($item['deskripsi'])) continue;

                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case', // Ubah jadi case agar masuk Technical Activities
                        // Jika monitoring, jangan hubungkan ke KPI 'Jumlah Case' agar tidak merusak perhitungan rata-rata
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'value_raw'          => $isMonitoring ? 0 : ($item['respons'] ?? 0),
                        'temuan_sendiri'     => !$isMonitoring && isset($item['temuan_sendiri']) ? 1 : 0,
                        'is_mandiri'         => 1, // Monitoring selalu mandiri
                        'pic_name'           => null,
                    ]);
                }
            }

            // B. Proses Case GPS (Sekarang SEMUA masuk tipe 'case')
            if ($request->has('case_gps')) {
                foreach ($request->case_gps as $item) {
                    if (empty($item['nama_kegiatan'])) continue;

                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");

                    $deskripsiGps = $item['nama_kegiatan'];
                    if (!empty($item['jumlah_kendaraan'])) {
                        $deskripsiGps .= " (Jumlah: " . $item['jumlah_kendaraan'] . " Kendaraan)";
                    }

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case', // Ubah jadi case agar masuk Technical Activities
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'deskripsi_kegiatan' => $deskripsiGps,
                        'value_raw'          => 0,
                        'is_mandiri'         => 1,
                    ]);
                }
            }

            // C. Proses General Activity (Hanya kegiatan lain-lain)
            if ($request->has('activity')) {
                foreach ($request->activity as $act) {
                    if (empty($act['deskripsi'])) continue;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity', // Ini baru masuk General Activities
                        'deskripsi_kegiatan' => $act['deskripsi'],
                    ]);
                }
            }
        }

        // ---------------------------------------------------------
        // PROSES DIVISI INFRASTRUKTUR (ID: 2)
        // ---------------------------------------------------------
        else if ($user->divisi_id == 2) {
            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            if ($request->has('infra_activity')) {
                foreach ($request->infra_activity as $infra) {
                    if (empty($infra['nama_kegiatan'])) continue;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'kategori'           => $infra['kategori'] ?? 'Network',
                        'deskripsi_kegiatan' => $infra['nama_kegiatan'] . ': ' . ($infra['deskripsi'] ?? '-'),
                        'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                    ]);
                }
            }
        }

        // ---------------------------------------------------------
        // PROSES DIVISI LAIN / BACKOFFICE
        // ---------------------------------------------------------
        else {
            $vBo = VariabelKpi::where('divisi_id', $user->divisi_id)->first();

            if ($request->has('bo_activity')) {
                foreach ($request->bo_activity as $bo) {
                    if (empty($bo['judul'])) continue;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'deskripsi_kegiatan' => $bo['judul'] . ': ' . ($bo['deskripsi'] ?? '-'),
                        'variabel_kpi_id'    => $vBo ? $vBo->id : null,
                    ]);
                }
            }
        }

        return redirect()->route('staff.input')->with('success', 'Laporan berhasil disimpan!');
    }
}
