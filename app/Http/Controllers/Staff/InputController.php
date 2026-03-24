<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VariabelKpi;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\Shift;
use Carbon\Carbon; // Pastikan Carbon di-import untuk logika waktu

class InputController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hariIni = now()->toDateString();
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Ambil data shift untuk form dropdown (Khusus TAC)
        $shifts = Shift::all();

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
                            'nomor_tiket' => $detail->nomor_tiket,
                            'respons' => $detail->waktu_respon_menit,
                            'temuan_sendiri' => (bool)$detail->temuan_sendiri,
                            'is_mandiri' => $detail->is_mandiri,
                            'pic_name' => $detail->pic_name,
                            'is_monitoring' => ($detail->waktu_respon_menit === null && !$detail->temuan_sendiri),
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
                    ['deskripsi' => 'Monitoring Network', 'nomor_tiket' => '', 'is_monitoring' => true, 'is_default' => true],
                    ['deskripsi' => '', 'nomor_tiket' => '', 'respons' => '', 'temuan_sendiri' => false, 'is_mandiri' => 1, 'pic_name' => '', 'is_monitoring' => false, 'is_default' => false]
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

        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows', 'isRejected', 'catatanManager', 'report', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $hariIni = now()->toDateString();
        $waktuSekarang = now();

        // 1. Validasi Awal & Validasi File Gambar
        if ($user->divisi_id == 1) { // Validasi khusus TAC
            $request->validate([
                'shift_id' => 'required',
                'case_network.*.bukti_respon_time' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'case_network.*.bukti_deteksi_dini' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'bukti_report_gps' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                // Validasi bukti dashboard DIHAPUS
            ]);

            if (!$request->has('case_network') && (!$request->has('case_gps') || empty(array_filter($request->case_gps, fn($item) => !empty($item['nama_kegiatan'])))) && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
            }
        } elseif ($user->divisi_id == 2) { // INFRA
            if (!$request->has('infra_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        } else { // BACKOFFICE
            if (!$request->has('bo_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        }

        // --- LOGIKA PENGECEKAN KETEPATAN WAKTU DASHBOARD KPI (Otomatis) ---
        $isDashboardOntime = 0;
        if ($user->divisi_id == 1 && $request->shift_id) {
            $shift = Shift::find($request->shift_id);
            if ($shift) {
                $jamPulang = Carbon::createFromTimeString($shift->jam_pulang);
                $jamMasuk = Carbon::createFromTimeString($shift->jam_masuk);

                // Cek jika Shift Malam (contoh: Masuk 20:00, Pulang 08:00)
                if ($jamPulang->lt($jamMasuk)) {
                    // Jika waktu submit lebih besar dari jam masuk (misal submit jam 22:00 malam),
                    // berarti jam kepulangannya ada di keesokan harinya.
                    if ($waktuSekarang->copy()->format('H:i:s') >= $shift->jam_masuk) {
                        $jamPulang->addDay();
                    }
                }

                // Tambahkan toleransi 2 jam dari jam pulang
                $batasMaksimal = $jamPulang->copy()->addHours(2);

                // Jika submit sekarang <= batas maksimal, berarti On Time
                if ($waktuSekarang->lte($batasMaksimal)) {
                    $isDashboardOntime = 1;
                }
            }
        }
        // -------------------------------------------------------------------

        // 2. Cari Laporan Hari Ini
        $report = DailyReport::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        // Mengelola Upload Bukti GPS (Hanya berlaku untuk TAC)
        $pathBuktiGps = $report->bukti_report_gps ?? null;

        if ($request->has('ada_laporan_gps') && $request->hasFile('bukti_report_gps')) {
            $pathBuktiGps = $request->file('bukti_report_gps')->store('kpi_evidences/gps', 'public');
        } elseif (!$request->has('ada_laporan_gps')) {
            // Jika toggle GPS dimatikan, kosongkan bukti gps
            $pathBuktiGps = null;
        }

        // 3. Simpan atau Update Tabel Laporan Utama
        if ($report) {
            if ($report->status == 'rejected') {
                $report->details()->delete();
                $report->update([
                    'status' => 'pending',
                    'catatan_manager' => null,
                    'shift_id' => $request->shift_id ?? $report->shift_id,
                    'is_gps_ontime' => ($request->has('ada_laporan_gps') && $request->has('is_gps_ontime')) ? 1 : 0,
                    'is_dashboard_ontime' => $isDashboardOntime, // Masukkan hasil pengecekan otomatis
                    'bukti_report_gps' => $pathBuktiGps,
                    'bukti_report_dashboard' => null, // Dikosongkan karena tidak perlu gambar lagi
                ]);
            }
        } else {
            $report = DailyReport::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'status'  => 'pending',
                'shift_id' => $request->shift_id,
                'is_gps_ontime' => ($request->has('ada_laporan_gps') && $request->has('is_gps_ontime')) ? 1 : 0,
                'is_dashboard_ontime' => $isDashboardOntime, // Masukkan hasil pengecekan otomatis
                'bukti_report_gps' => $pathBuktiGps,
                'bukti_report_dashboard' => null, // Dikosongkan
            ]);
        }

        // 4. Proses Simpan Detail Berdasarkan Divisi
        if ($user->divisi_id == 1) { // TAC
            $vCount = VariabelKpi::where('divisi_id', 1)->where('nama_variabel', 'Jumlah Case Harian')->first();

            // A. Case Network
            if ($request->has('case_network')) {
                foreach ($request->case_network as $index => $item) {
                    if (empty($item['deskripsi'])) continue;
                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");

                    // Ambil status apakah ini deteksi dini (temuan sendiri)
                    $isTemuanSendiri = !$isMonitoring && isset($item['temuan_sendiri']) ? 1 : 0;

                    $pathRespon = null;
                    $pathDeteksi = null;

                    // HANYA simpan bukti respon JIKA BUKAN deteksi dini
                    if (!$isTemuanSendiri && $request->hasFile("case_network.{$index}.bukti_respon_time")) {
                        $pathRespon = $request->file("case_network.{$index}.bukti_respon_time")->store('kpi_evidences/respon', 'public');
                    }

                    // HANYA simpan bukti deteksi JIKA ITU deteksi dini
                    if ($isTemuanSendiri && $request->hasFile("case_network.{$index}.bukti_deteksi_dini")) {
                        $pathDeteksi = $request->file("case_network.{$index}.bukti_deteksi_dini")->store('kpi_evidences/deteksi', 'public');
                    }

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'kategori'           => 'Network',
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'value_raw'          => '0',
                        'nomor_tiket'        => $isMonitoring ? null : ($item['nomor_tiket'] ?? null),
                        // Kosongkan waktu respon (null) jika itu deteksi dini
                        'waktu_respon_menit' => ($isMonitoring || $isTemuanSendiri) ? null : ($item['respons'] ?? 0),
                        'temuan_sendiri'     => $isTemuanSendiri,
                        'is_mandiri'         => $isMonitoring ? 1 : ($item['is_mandiri'] ?? 1),
                        'pic_name'           => (!$isMonitoring && isset($item['is_mandiri']) && $item['is_mandiri'] == 0) ? ($item['pic_name'] ?? null) : null,
                        'bukti_respon_time'  => $pathRespon,
                        'bukti_deteksi_dini' => $pathDeteksi,
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
        }
        // ... (Logika INFRA & BO tetap ada sesuai aslinya)
        else if ($user->divisi_id == 2) {
            // LOGIKA INFRA
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
        } else {
            // LOGIKA BACKOFFICE
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
