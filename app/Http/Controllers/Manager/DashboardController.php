<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;
// Pastikan Anda memiliki model ini (sesuai struktur DB sebelumnya)
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedDivisi = $request->get('divisi_id', '1');
        $divisis = Divisi::all();

        // 1. LOGIKA FILTER TANGGAL
        $filter = $request->get('filter', 'monthly');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        switch ($filter) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case 'weekly':
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case 'custom':
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();
                break;
            default: // monthly
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
        }

        // 2. LOGIKA FILTER STAFF
        $selectedUserId = $request->get('user_id', 'all');

        $staffQuery = User::where('divisi_id', $selectedDivisi)->where('role', 'staff');
        if ($selectedUserId !== 'all') {
            $staffQuery->where('id', $selectedUserId);
        }
        $staffs = $staffQuery->get();
        $staffIds = $staffs->pluck('id')->toArray();

        // 3. AMBIL DATA UTAMA (Berdasarkan filter divisi, tanggal, dan staf)
        $reports = DailyReport::with('details')
            ->whereIn('user_id', $staffIds)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();

        $allDetails = collect();
        foreach ($reports as $report) {
            foreach ($report->details as $detail) {
                // Sisipkan tanggal report ke detail agar mudah ditrending
                $detail->tanggal_report = $report->tanggal;
                $allDetails->push($detail);
            }
        }

        // Label Hari untuk Trend Line/Area Chart
        $trendLabels = [];
        $diffInDays = $start->diffInDays($end);
        for ($i = 0; $i <= $diffInDays; $i++) {
            $trendLabels[] = (clone $start)->addDays($i)->format('Y-m-d');
        }

        // ========================================================================
        // A. METRIK EKSEKUTIF (Semua Divisi - Compliance & Evaluation)
        // ========================================================================
        $gpsReports = $reports->whereNotNull('bukti_report_gps');

        $compliance = [
            'dashboard' => [
                'ontime' => $reports->where('is_dashboard_ontime', 1)->count(),
                'late' => $reports->where('is_dashboard_ontime', 0)->count()
            ],
            'gps' => [
                // Hanya menghitung dari populasi yang ada bukti GPS-nya
                'ontime' => $gpsReports->where('is_gps_ontime', 1)->count(),
                'late' => $gpsReports->where('is_gps_ontime', 0)->count()
            ]
        ];

        // Simulasi/Ambil Evaluasi Teknis Rata-rata Tim (Radar Chart)
        $assessments = TechnicalAssessment::whereIn('user_id', $staffIds)->get();
        $evaluation = [
            'technical' => [
                'network' => $assessments->avg('pemahaman_network') ?? 0,
                'hardware' => $assessments->avg('pemahaman_hardware') ?? 0,
                'software' => $assessments->avg('pemahaman_software') ?? 0,
                'cctv' => $assessments->avg('pemahaman_cctv') ?? 0,
                'gps' => $assessments->avg('pemahaman_gps') ?? 0,
            ],
            // PERBAIKAN: Menggunakan tanggal_survey, COUNT(*) untuk total survey, dan AVG(rating)
            'feedback' => CustomerFeedback::selectRaw('DATE(tanggal_survey) as date, COUNT(*) as total_survey, AVG(rating) as avg_rating')
                ->whereIn('user_id', $staffIds)
                ->whereBetween('tanggal_survey', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->groupBy('date')
                ->orderBy('date', 'asc') // Urutkan dari tanggal terlama agar chart rapi
                ->get()
        ];

        // ========================================================================
        // WADAH CHART DATA
        // ========================================================================
        $chartData = [
            'compliance' => $compliance,
            'evaluation' => $evaluation,
            'trend_labels' => array_map(fn($d) => Carbon::parse($d)->format('d M'), $trendLabels),
            'staff_labels' => $staffs->pluck('nama_lengkap')->toArray(),
            'tac' => [],
            'infra' => [],
            'bo' => []
        ];

        // ========================================================================
        // B. LOGIKA DIVISI 1 (TAC)
        // ========================================================================
        if ($selectedDivisi == '1') {
            // Memfilter data Network yang TIDAK mengandung kata "monitoring"
            $networkCases = $allDetails->where('kategori', 'Network')
                ->where('tipe_kegiatan', 'case')
                ->filter(fn($d) => !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring'));

            // Memfilter data GPS yang TIDAK mengandung kata "monitoring"
            $gpsCases = $allDetails->where('kategori', 'GPS')
                ->where('tipe_kegiatan', 'case')
                ->filter(fn($d) => !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring'));

            // 1. Baris 1: Overview
            $chartData['tac']['temuan_vs_laporan'] = [
                $networkCases->where('temuan_sendiri', 1)->count(), // Temuan
                $networkCases->where('temuan_sendiri', 0)->count()  // Laporan
            ];
            $chartData['tac']['mandiri_vs_eskalasi'] = [
                $networkCases->where('is_mandiri', 1)->count(), // Mandiri
                $networkCases->where('is_mandiri', 0)->count()  // Eskalasi
            ];

            // 2. Baris 2: Rasio & Limit Waktu
            $chartData['tac']['case_vs_activity'] = [
                $allDetails->where('tipe_kegiatan', 'case')->count(),
                $allDetails->where('tipe_kegiatan', 'activity')->count()
            ];
            $chartData['tac']['network_vs_gps'] = [
                $networkCases->count(),
                $gpsCases->count()
            ];

            // Gauge Chart Limit Waktu (Ambil kasus yang BUKAN temuan sendiri karena temuan waktu responnya 0)
            $avgTime = $networkCases->where('temuan_sendiri', 0)->avg('waktu_respon_menit') ?? 0;
            $chartData['tac']['avg_response_time'] = round($avgTime, 1);

            // 3. Baris 3: Kinerja Individu (Leaderboard Staff)
            $barTotalCase = [];
            $barAvgResponse = [];
            $barMandiri = [];
            $barTemuan = [];

            foreach ($staffs as $st) {
                $stNetCases = $networkCases->where('dailyReport.user_id', $st->id);
                $stGpsCases = $gpsCases->where('dailyReport.user_id', $st->id);

                $barTotalCase[] = $stNetCases->count() + $stGpsCases->count();
                $barAvgResponse[] = round($stNetCases->where('temuan_sendiri', 0)->avg('waktu_respon_menit') ?? 0, 1);
                $barMandiri[] = $stNetCases->where('is_mandiri', 1)->count();
                $barTemuan[] = $stNetCases->where('temuan_sendiri', 1)->count();
            }

            $chartData['tac']['staff_total_case'] = $barTotalCase;
            $chartData['tac']['staff_avg_response'] = $barAvgResponse;
            $chartData['tac']['staff_mandiri'] = $barMandiri;
            $chartData['tac']['staff_temuan'] = $barTemuan;

            // 4. Baris 4: Tren Harian (Line & Area)
            $trendActivity = [];
            $trendCase = [];
            $trendQtyGps = [];

            foreach ($trendLabels as $date) {
                // PERBAIKAN: Gunakan Carbon::parse agar format waktunya benar-benar match Y-m-d
                $dayDetails = $allDetails->filter(function ($d) use ($date) {
                    return \Carbon\Carbon::parse($d->tanggal_report)->format('Y-m-d') === $date;
                });

                $trendActivity[] = $dayDetails->where('tipe_kegiatan', 'activity')->count();
                $trendCase[] = $dayDetails->where('tipe_kegiatan', 'case')->count();

                // Area Chart GPS
                $gpsToday = $dayDetails->where('kategori', 'GPS')->where('tipe_kegiatan', 'case');
                $qty = 0;
                foreach ($gpsToday as $g) {
                    $qty += is_numeric($g->value_raw) ? (int)$g->value_raw : 0;
                }
                $trendQtyGps[] = $qty;
            }

            $chartData['tac']['trend_activity'] = $trendActivity;
            $chartData['tac']['trend_case'] = $trendCase;
            $chartData['tac']['trend_qty_gps'] = $trendQtyGps;
        }

        // ========================================================================
        // C. LOGIKA DIVISI 2 (INFRA)
        // ========================================================================
        elseif ($selectedDivisi == '2') {
            $categories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
            $chartData['infra']['categories'] = $categories;

            // 1. Donut Chart (Distribusi Kategori)
            $donutInfra = [];
            foreach ($categories as $cat) {
                $donutInfra[] = $allDetails->where('kategori', $cat)->count();
            }
            $chartData['infra']['donut_kategori'] = $donutInfra;

            // 2. Stacked Bar Chart (Distribusi Kategori per Staf)
            $stackedData = [];
            foreach ($categories as $cat) {
                $dataStaff = [];
                foreach ($staffs as $st) {
                    $dataStaff[] = $allDetails->where('dailyReport.user_id', $st->id)->where('kategori', $cat)->count();
                }
                $stackedData[] = [
                    'name' => $cat,
                    'data' => $dataStaff
                ];
            }
            $chartData['infra']['stacked_staff'] = $stackedData;

            // 3. Line Chart (Tren Harian Kategori)
            $trendInfra = [];
            foreach ($categories as $cat) {
                $dataHari = [];
                foreach ($trendLabels as $date) {
                    // PERBAIKAN: Gunakan Carbon::parse() untuk memastikan format tanggal sama persis
                    $count = $allDetails->filter(function ($d) use ($date, $cat) {
                        return \Carbon\Carbon::parse($d->tanggal_report)->format('Y-m-d') === $date
                            && $d->kategori === $cat;
                    })->count();

                    $dataHari[] = $count;
                }
                $trendInfra[] = [
                    'name' => $cat,
                    'data' => $dataHari
                ];
            }
            $chartData['infra']['trend_kategori'] = $trendInfra;
        }

        // ========================================================================
        // D. LOGIKA DIVISI 3 (BACKOFFICE)
        // ========================================================================
        elseif ($selectedDivisi == '3') {
            // Asumsi Backoffice menggunakan format "Kategori Pekerjaan: Deskripsi" di deskripsi_kegiatannya
            // Kita akan mengekstrak kata pertama sebelum titik dua ":" sebagai "Tipe Pekerjaan"
            $boTypes = [];
            foreach ($allDetails as $d) {
                $parts = explode(':', $d->deskripsi_kegiatan);
                $type = trim($parts[0]);
                if (!isset($boTypes[$type])) {
                    $boTypes[$type] = 0;
                }
                $boTypes[$type]++;
            }

            // 1. Donut Tipe Pekerjaan
            $chartData['bo']['donut_labels'] = array_keys($boTypes);
            $chartData['bo']['donut_series'] = array_values($boTypes);

            // 2. Bar Chart Total Volume per Staf
            $barVolume = [];
            foreach ($staffs as $st) {
                $barVolume[] = $allDetails->where('dailyReport.user_id', $st->id)->count();
            }
            $chartData['bo']['staff_volume'] = $barVolume;

            // 3. Line Chart Tren Produktivitas Harian
            $trendVolume = [];
            foreach ($trendLabels as $date) {
                $trendVolume[] = $allDetails->where('tanggal_report', $date)->count();
            }
            $chartData['bo']['trend_volume'] = $trendVolume;
        }

        // ========================================================================
        // KEMBALIKAN RESPONSE JSON ATAU VIEW
        // ========================================================================
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $chartData
            ]);
        }

        // Jika bukan AJAX, kirim staf untuk dropdown filter dan data awal untuk load chart
        $allStaffs = User::where('divisi_id', $selectedDivisi)->where('role', 'staff')->get();
        return view('manager.dashboard', compact(
            'divisis',
            'selectedDivisi',
            'allStaffs',
            'chartData'
        ));
    }
}
