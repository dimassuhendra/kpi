<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VariabelKpi;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\Shift;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;
use Carbon\Carbon;

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

        $myAssessments = TechnicalAssessment::where('user_id', $user->id)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->get();

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
                            'waktu_respon_menit' => $detail->waktu_respon_menit, // Diubah agar sesuai dengan x-model="row.waktu_respon_menit"
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
                    ['deskripsi' => 'Monitoring Network', 'nomor_tiket' => '', 'waktu_respon_menit' => '', 'temuan_sendiri' => false, 'is_mandiri' => 1, 'pic_name' => '', 'is_monitoring' => true, 'is_default' => true],
                    ['deskripsi' => '', 'nomor_tiket' => '', 'waktu_respon_menit' => '', 'temuan_sendiri' => false, 'is_mandiri' => 1, 'pic_name' => '', 'is_monitoring' => false, 'is_default' => false]
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
        } elseif (in_array($user->divisi_id, [6, 8])) { // 6: BACKOFFICE, 8: PURCHASING (Sesuaikan ID-nya)
            if ($isRejected) {
                foreach ($report->details as $detail) {
                    $formattedRows['bo'][] = ['judul' => $detail->deskripsi_kegiatan, 'deskripsi' => ''];
                }
            } else {
                $formattedRows['bo'] = [['judul' => '', 'deskripsi' => '']];
            }
        }

        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows', 'isRejected', 'catatanManager', 'report', 'shifts', 'myAssessments'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $hariIni = $waktuSekarang->toDateString();

        // 1. Validasi Awal (Ubah image menjadi string path)
        if ($user->divisi_id == 1) {
            $request->validate([
                'shift_id' => 'required',
                'case_network.*.bukti_respon_time_path' => 'nullable|string',
                'case_network.*.bukti_deteksi_dini_path' => 'nullable|string',
                'bukti_report_gps_path' => 'nullable|string',
            ]);

            if (!$request->has('case_network') && (!$request->has('case_gps') || empty(array_filter($request->case_gps, fn($item) => !empty($item['nama_kegiatan'])))) && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
            }
        } elseif ($user->divisi_id == 2) {
            if (!$request->has('infra_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        } else {
            if (!$request->has('bo_activity')) return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
        }

        // --- LOGIKA PENGECEKAN KETEPATAN WAKTU DASHBOARD KPI (GMT+7) ---
        $isDashboardOntime = 0;
        if ($user->divisi_id == 1 && $request->shift_id) {
            $shift = Shift::find($request->shift_id);
            if ($shift) {
                $jamPulang = Carbon::createFromTimeString($shift->jam_pulang, 'Asia/Jakarta');
                $jamMasuk = Carbon::createFromTimeString($shift->jam_masuk, 'Asia/Jakarta');

                if ($jamPulang->lt($jamMasuk)) {
                    if ($waktuSekarang->copy()->format('H:i:s') >= $shift->jam_masuk) {
                        $jamPulang->addDay();
                    }
                }

                $batasMaksimal = $jamPulang->copy()->addHours(2);

                if ($waktuSekarang->lte($batasMaksimal)) {
                    $isDashboardOntime = 1;
                }
            }
        }

        // 2. Cari Laporan Hari Ini
        $report = DailyReport::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        // Mengambil path Bukti GPS dari hidden input
        $pathBuktiGps = $report->bukti_report_gps ?? null;
        if ($request->has('ada_laporan_gps') && $request->filled('bukti_report_gps_path')) {
            $pathBuktiGps = $request->input('bukti_report_gps_path');
        } elseif (!$request->has('ada_laporan_gps')) {
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
                    'is_dashboard_ontime' => $isDashboardOntime,
                    'bukti_report_gps' => $pathBuktiGps,
                    'bukti_report_dashboard' => null,
                ]);
            }
        } else {
            $report = DailyReport::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'status'  => 'pending',
                'shift_id' => $request->shift_id,
                'is_gps_ontime' => ($request->has('ada_laporan_gps') && $request->has('is_gps_ontime')) ? 1 : 0,
                'is_dashboard_ontime' => $isDashboardOntime,
                'bukti_report_gps' => $pathBuktiGps,
                'bukti_report_dashboard' => null,
            ]);
        }

        // ==========================================
        // FITUR BARU: LOGIKA PENYIMPANAN LEMBUR
        // ==========================================
        if ($request->has('is_lembur') && $request->is_lembur == '1') {
            $request->validate([
                'lembur_activity.*.waktu_mulai' => 'required|date',
                'lembur_activity.*.waktu_selesai' => 'required|date|after:lembur_activity.*.waktu_mulai',
                'lembur_activity.*.detail' => 'required|string',
                'lembur_activity.*.foto_path' => 'required|string',
            ], [
                'lembur_activity.*.waktu_selesai.after' => 'Waktu selesai lembur harus lebih dari waktu mulai pada baris terkait.'
            ]);

            // Bersihkan data lembur lama untuk hari ini (jika ada) sebelum insert yang baru
            \App\Models\LemburReport::where('daily_report_id', $report->id)->delete();

            if ($request->has('lembur_activity')) {
                foreach ($request->lembur_activity as $lembur) {
                    if (empty($lembur['detail'])) continue;

                    \App\Models\LemburReport::create([
                        'daily_report_id' => $report->id,
                        'waktu_mulai' => $lembur['waktu_mulai'],
                        'waktu_selesai' => $lembur['waktu_selesai'],
                        'detail_pekerjaan' => $lembur['detail'],
                        'foto_dokumentasi' => $lembur['foto_path'],
                    ]);
                }
            }
        } else {
            // Jika switch dimatikan, hapus semua data lembur hari ini
            \App\Models\LemburReport::where('daily_report_id', $report->id)->delete();
        }

        // 4. Proses Simpan Detail Berdasarkan Divisi
        if ($user->divisi_id == 1) { // TAC
            $vCount = VariabelKpi::where('divisi_id', 1)->where('nama_variabel', 'Jumlah Case Harian')->first();

            // A. Case Network
            if ($request->has('case_network')) {
                foreach ($request->case_network as $index => $item) {
                    if (empty($item['deskripsi'])) continue;

                    $isMonitoring = (isset($item['is_monitoring']) && $item['is_monitoring'] == "1");
                    $isTemuanSendiri = !$isMonitoring && isset($item['temuan_sendiri']) ? 1 : 0;

                    // Menggunakan path dari hidden input
                    $pathRespon = (!$isTemuanSendiri && !empty($item['bukti_respon_time_path'])) ? $item['bukti_respon_time_path'] : null;
                    $pathDeteksi = ($isTemuanSendiri && !empty($item['bukti_deteksi_dini_path'])) ? $item['bukti_deteksi_dini_path'] : null;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => (!$isMonitoring && $vCount) ? $vCount->id : null,
                        'kategori'           => 'Network',
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'nomor_tiket'        => $isMonitoring ? null : ($item['nomor_tiket'] ?? null),
                        'temuan_sendiri'     => $isTemuanSendiri,
                        'is_mandiri'         => $isMonitoring ? 1 : ($item['is_mandiri'] ?? 1),
                        'pic_name'           => (!$isMonitoring && isset($item['is_mandiri']) && $item['is_mandiri'] == 0) ? ($item['pic_name'] ?? null) : null,
                        'bukti_respon_time'  => $pathRespon,
                        'bukti_deteksi_dini' => $pathDeteksi,
                        'value_raw'          => null,
                        'waktu_respon_menit' => $isMonitoring ? null : ($isTemuanSendiri ? 0 : ($item['waktu_respon_menit'] ?? 0)),
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
                        'is_mandiri'         => 1,
                        'kategori'           => 'GPS',
                        'value_raw'          => $isMonitoring ? 'ALL' : ($item['jumlah_kendaraan'] ?? 0),
                        'waktu_respon_menit' => null,
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
                        'value_raw'          => null,
                        'waktu_respon_menit' => null,
                    ]);
                }
            }
        } else if ($user->divisi_id == 2) {
            // LOGIKA INFRA (UPDATE ASYNC UPLOAD)
            $request->validate([
                'infra_activity.*.foto_dokumentasi_path' => 'nullable|string',
            ]);

            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            if ($request->has('infra_activity')) {
                foreach ($request->infra_activity as $infra) {
                    if (empty($infra['nama_kegiatan'])) continue;

                    $kategori = $infra['kategori'] ?? 'Network';
                    $tipe = ($kategori == 'Lainnya') ? 'activity' : 'case';

                    // Menggunakan path dari hidden input hasil async upload
                    $pathFoto = !empty($infra['foto_dokumentasi_path']) ? $infra['foto_dokumentasi_path'] : null;

                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => $tipe,
                        'kategori'           => $kategori,
                        'nama_kegiatan'      => $infra['nama_kegiatan'],
                        'deskripsi_kegiatan' => $infra['deskripsi'],
                        'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                        'is_mandiri'         => 1,
                        'value_raw'          => null,
                        'waktu_respon_menit' => null,
                        'foto_dokumentasi'   => $pathFoto,
                    ]);
                }
            }
        } else {
            // LOGIKA BACKOFFICE
            $vKpi = VariabelKpi::where('divisi_id', $user->divisi_id)->first();

            if ($request->has('bo_activity')) {
                foreach ($request->bo_activity as $bo) {
                    if (empty($bo['judul'])) continue;
                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'deskripsi_kegiatan' => $bo['judul'] . ': ' . ($bo['deskripsi'] ?? '-'),
                        'variabel_kpi_id'    => $vKpi ? $vKpi->id : null,
                        'value_raw'          => null,
                        'waktu_respon_menit' => null,
                    ]);
                }
            }
        }

        // Jika form dikirim via AJAX (Progress Bar)
        if ($request->ajax()) {
            session()->flash('success', 'Laporan berhasil dikirim!');
            return response()->json(['redirect' => route('staff.input')]);
        }

        return redirect()->route('staff.input')->with('success', 'Laporan berhasil dikirim!');
    }

    public function uploadAsync(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Validasi per file tetap 2MB
            'folder' => 'required|string'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = $request->folder; // 'gps', 'respon', atau 'deteksi'

            // Langsung simpan ke folder final
            $path = $file->store("kpi_evidences/{$folder}", 'public');

            return response()->json(['path' => $path], 200);
        }

        return response()->json(['error' => 'File tidak ditemukan'], 400);
    }

    // Fungsi untuk menyimpan Customer Feedback (Rating)
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'nomor_tiket' => 'required|string|max:255',
            'nama_pelanggan' => 'required|string|max:255',
            'tanggal_survey' => 'required|date',
            'rating' => 'required|integer|min:1|max:5',
            'bukti_survey' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan gambar bukti survey
        $pathBukti = $request->file('bukti_survey')->store('kpi_evidences/feedbacks', 'public');

        CustomerFeedback::create([
            'user_id' => Auth::id(),
            'nomor_tiket' => $request->nomor_tiket,
            'nama_pelanggan' => $request->nama_pelanggan,
            'tanggal_survey' => $request->tanggal_survey,
            'rating' => $request->rating,
            'bukti_survey' => $pathBukti,
        ]);

        return redirect()->route('staff.input')->with('success', 'Data Rating Pelanggan berhasil disimpan!');
    }

    // Fungsi untuk menyimpan Technical Assessment (Nilai Kuis)
    public function storeAssessment(Request $request)
    {
        $request->validate([
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2020',
            'jumlah_soal' => 'required|integer|min:1',
            'jumlah_benar' => 'required|integer|min:0|lte:jumlah_soal', // jumlah benar tidak boleh lebih besar dari soal
            'bukti_kuis' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan gambar bukti kuis
        $pathBukti = $request->file('bukti_kuis')->store('kpi_evidences/assessments', 'public');

        TechnicalAssessment::create([
            'user_id' => Auth::id(),
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'jumlah_soal' => $request->jumlah_soal,
            'jumlah_benar' => $request->jumlah_benar,
            'bukti_kuis' => $pathBukti,
        ]);

        return redirect()->route('staff.input')->with('success', 'Nilai Asesmen Kuis berhasil disimpan!');
    }
}
