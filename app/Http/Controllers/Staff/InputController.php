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
        $hariIni = now()->toDateString();
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Cari laporan hari ini (baik yang pending atau rejected)
        $report = DailyReport::with('details')
            ->where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        // Syarat revisi: laporan ada dan statusnya 'rejected'
        $isRejected = $report && $report->status == 'rejected';
        $catatanManager = $report->catatan_manager ?? null;

        // Inisialisasi struktur default untuk Alpine.js
        $formattedRows = [
            'network' => [],
            'gps' => [],
            'activities' => [],
            'infra' => [],
            'bo' => []
        ];

        if ($user->divisi_id == 1) { // TAC
            if ($isRejected) {
                foreach ($report->details as $detail) {
                    if ($detail->tipe_kegiatan == 'case' && $detail->kategori == 'Network') {
                        $formattedRows['network'][] = [
                            'deskripsi' => $detail->deskripsi_kegiatan,
                            'respons' => $detail->value_raw,
                            'temuan_sendiri' => (bool)$detail->temuan_sendiri,
                            'is_mandiri' => $detail->is_mandiri,
                            'pic_name' => $detail->pic_name,
                            'is_monitoring' => ($detail->value_raw == 0 && !$detail->temuan_sendiri),
                            'is_default' => ($detail->deskripsi_kegiatan == 'Monitoring Network')
                        ];
                    } elseif ($detail->tipe_kegiatan == 'case' && $detail->kategori == 'GPS') {
                        $formattedRows['gps'][] = [
                            'nama_kegiatan' => $detail->deskripsi_kegiatan,
                            'jumlah_kendaraan' => $detail->value_raw,
                            'is_monitoring' => ($detail->value_raw === 'ALL'),
                            'is_default' => ($detail->deskripsi_kegiatan == 'Monitoring GPS')
                        ];
                    } else {
                        $formattedRows['activities'][] = ['deskripsi' => $detail->deskripsi_kegiatan];
                    }
                }
            } else {
                // Default awal jika tidak ada reject/laporan baru
                $formattedRows['network'] = [
                    ['deskripsi' => 'Monitoring Network', 'is_monitoring' => true, 'is_default' => true],
                    ['deskripsi' => '', 'respons' => '', 'temuan_sendiri' => false, 'is_mandiri' => 1, 'pic_name' => '', 'is_monitoring' => false, 'is_default' => false]
                ];
                $formattedRows['gps'] = [
                    ['nama_kegiatan' => 'Monitoring GPS', 'jumlah_kendaraan' => '', 'is_monitoring' => true, 'is_default' => true],
                    ['nama_kegiatan' => '', 'jumlah_kendaraan' => '', 'is_monitoring' => false, 'is_default' => false]
                ];
                $formattedRows['activities'] = [['deskripsi' => '']];
            }
        } elseif ($user->divisi_id == 2) { // INFRA
            if ($isRejected) {
                foreach ($report->details as $detail) {
                    $formattedRows['infra'][] = [
                        'kategori' => $detail->kategori,
                        'nama_kegiatan' => $detail->deskripsi_kegiatan,
                        'deskripsi' => ''
                    ];
                }
            } else {
                $formattedRows['infra'] = [['kategori' => 'Network', 'nama_kegiatan' => '', 'deskripsi' => '']];
            }
        } else { // BACKOFFICE
            if ($isRejected) {
                foreach ($report->details as $detail) {
                    $formattedRows['bo'][] = ['judul' => $detail->deskripsi_kegiatan, 'deskripsi' => ''];
                }
            } else {
                $formattedRows['bo'] = [['judul' => '', 'deskripsi' => '']];
            }
        }

        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows', 'isRejected', 'catatanManager'));
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

        // 2. Cari laporan hari ini (untuk mode revisi/update)
        $report = DailyReport::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        if ($report) {
            // Jika merevisi laporan yang di-reject, bersihkan detail lama dan reset status
            if ($report->status == 'rejected') {
                $report->details()->delete();
                $report->update([
                    'status' => 'pending',
                    'catatan_manager' => null, // Bersihkan catatan manager setelah direvisi
                ]);
            }
        } else {
            // Jika benar-benar laporan baru
            $report = DailyReport::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'status'  => 'pending',
            ]);
        }

        // 3. Proses Simpan Detail Berdasarkan Divisi
        if ($user->divisi_id == 1) { // TAC
            $vCount = VariabelKpi::where('divisi_id', 1)->where('nama_variabel', 'Jumlah Case Harian')->first();

            // A. Case Network
            if ($request->has('case_network')) {
                foreach ($request->case_network as $item) {
                    if (empty($item['deskripsi'])) continue;
                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'value_raw'          => $isMonitoring ? 0 : ($item['respons'] ?? 0),
                        'temuan_sendiri'     => !$isMonitoring && isset($item['temuan_sendiri']) ? 1 : 0,
                        'is_mandiri'         => $isMonitoring ? 1 : ($item['is_mandiri'] ?? 1),
                        'kategori'           => 'Network',
                        'pic_name'           => (!$isMonitoring && isset($item['is_mandiri']) && $item['is_mandiri'] == 0) ? ($item['pic_name'] ?? null) : null,
                    ]);
                }
            }

            // B. Case GPS
            if ($request->has('case_gps')) {
                foreach ($request->case_gps as $item) {
                    if (empty($item['nama_kegiatan'])) continue;
                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'deskripsi_kegiatan' => $item['nama_kegiatan'],
                        'value_raw'          => $isMonitoring ? 'ALL' : ($item['jumlah_kendaraan'] ?? 0),
                        'is_mandiri'         => 1,
                        'kategori'           => 'GPS',
                    ]);
                }
            }

            // C. General Activity
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
        } else if ($user->divisi_id == 2) { // INFRA
            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            if ($request->has('infra_activity')) {
                foreach ($request->infra_activity as $infra) {
                    if (empty($infra['nama_kegiatan'])) continue;
                    $kategori = $infra['kategori'] ?? 'Network';
                    $tipe = ($kategori == 'Lainnya') ? 'activity' : 'case';

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => $tipe,
                        'kategori'           => $kategori,
                        'deskripsi_kegiatan' => $infra['nama_kegiatan'] . (isset($infra['deskripsi']) ? ': ' . $infra['deskripsi'] : ''),
                        'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                        'value_raw'          => 0,
                        'is_mandiri'         => 1,
                    ]);
                }
            }
        } else { // BACKOFFICE / LAINNYA
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

        return redirect()->route('staff.input')->with('success', 'Laporan berhasil diperbarui!');
    }
}
