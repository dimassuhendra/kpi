<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedDivisi = $request->get('divisi_id', '1');
        $divisis = Divisi::all();

        // Logika Filter Tanggal
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

        // Variabel Default
        $infraWorkload = collect();
        $staffInfraData = collect();
        $availableCategories = [];
        $staffChartData = collect();
        $workloadMix = ['case' => 0, 'activity' => 0];
        $trendLabels = [];
        $trendCases = [];
        $trendActivities = [];
        $leaderboard = collect();
        $infraTrendData = [];
        $staffWorkloadDist = collect();
        $summaryData = [
            'total_case' => 0,
            'avg_time' => 0,
            'mandiri' => 0,
            'bantuan' => 0,
            'proaktif' => 0,
            'penugasan' => 0
        ];

        $stats = [
            'pending' => DailyReport::where('status', 'pending')->count(),
            'active_today' => DailyReport::whereDate('tanggal', today())->distinct('user_id')->count('user_id'),
            'resolved_month' => 0,
            'avg_response_time' => 0,
        ];

        $heatmapData = DailyReport::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->whereYear('tanggal', $start->year)
            ->groupBy('date')->pluck('count', 'date');

        // --- LOGIKA DIVISI 2 (INFRA) ---
        if ($selectedDivisi == '2') {
            $allDetails = KegiatanDetail::with('dailyReport')->whereHas('dailyReport', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end])
                    ->whereHas('user', fn($u) => $u->where('divisi_id', 2));
            })->get();

            $infraWorkload = $allDetails->whereNotNull('kategori')->groupBy('kategori')->map->count();
            $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];

            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $trendLabels[] = $dateObj->format('d M');
                foreach ($availableCategories as $cat) {
                    $infraTrendData[$cat][] = $allDetails->filter(
                        fn($detail) =>
                        $detail->kategori === $cat && Carbon::parse($detail->dailyReport->tanggal)->isSameDay($dateObj)
                    )->count();
                }
            }

            $staffInfraData = User::where('divisi_id', 2)->where('role', 'staff')->get()->map(function ($u) use ($allDetails, $availableCategories) {
                $res = ['nama' => $u->nama_lengkap];
                foreach ($availableCategories as $cat) {
                    $res[$cat] = $allDetails->where('kategori', $cat)->where('dailyReport.user_id', $u->id)->count();
                }
                return $res;
            });

            $leaderboard = User::where('role', 'staff')->where('divisi_id', 2)
                ->withCount(['details as total_activity' => fn($q) => $q->whereHas('dailyReport', fn($qr) => $qr->whereBetween('tanggal', [$start, $end]))])
                ->orderByDesc('total_activity')->take(5)->get();

            // --- LOGIKA DIVISI 1 (TAC) ---
            // --- LOGIKA DIVISI 1 (TAC) ---
        } elseif ($selectedDivisi == '1') {
            $allDetailsTAC = KegiatanDetail::with('dailyReport')->whereHas('dailyReport', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end])
                    ->whereHas('user', fn($u) => $u->where('divisi_id', 1));
            })->get();

            // 1. Sanitasi Data: Paksa value_raw menjadi numerik
            $allDetailsTAC->transform(function ($item) {
                $item->value_raw = is_numeric($item->value_raw) ? (float) $item->value_raw : 0;
                return $item;
            });

            // ========================================================================
            // BENTENG FILTER UTAMA: Pisahkan Murni Network vs GPS
            // ========================================================================

            $filteredDetails = $allDetailsTAC->filter(function ($item) {
                $title = trim($item->deskripsi_kegiatan);
                return !in_array($title, ['Monitoring GPS', 'Monitoring Network']);
            });
            
            $networkDetailsTAC = $allDetailsTAC->where('kategori', 'Network');

            // Sub-kategori untuk Network
            $caseNetwork = $networkDetailsTAC->where('tipe_kegiatan', 'case');
            $activityNetwork = $networkDetailsTAC->where('tipe_kegiatan', 'activity');

            // GPS dipisah dan dikarantina (Hanya dihitung jika nanti diperlukan di view lain)
            $caseGPS = $allDetailsTAC->where('tipe_kegiatan', 'case')->where('kategori', 'GPS');

            // 2. Terapkan data murni Network ke metrik utama Dashboard
            $stats['resolved_month'] = $caseNetwork->count();
            $stats['avg_response_time'] = $caseNetwork->avg('value_raw') ?? 0;

            $workloadMix = [
                'case' => $caseNetwork->count(),
                'activity' => $activityNetwork->count()
            ];

            // 3. Data Summary (Donut Chart & Threshold) khusus Network
            $summaryData = [
                'total_case' => $caseNetwork->count(),
                'avg_time' => round($stats['avg_response_time'], 2),
                'network_count' => $caseNetwork->count(),
                'network_avg'   => round($caseNetwork->avg('value_raw') ?? 0, 1),
                'gps_count'     => $caseGPS->sum('value_raw'),
                'gps_avg'       => 0,
                'mandiri' => $caseNetwork->where('is_mandiri', 1)->count(),
                'bantuan' => $caseNetwork->where('is_mandiri', 0)->count(),
                'proaktif' => $caseNetwork->where('temuan_sendiri', 1)->count(),
                'penugasan' => $caseNetwork->where('temuan_sendiri', 0)->count(),
            ];

            // 4. Trend Harian (Grafik Garis) khusus Network
            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $dateStr = $dateObj->toDateString();
                $trendLabels[] = $dateObj->format('d M');

                // Filter data harian HANYA dari sumber Network
                $dayDetails = $networkDetailsTAC->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr);

                $trendCases[] = $dayDetails->where('tipe_kegiatan', 'case')->count();
                $trendActivities[] = $dayDetails->where('tipe_kegiatan', 'activity')->count();
            }

            // 5. Mapping Data Staff (Mini Charts & Dropdown Staff) khusus Network
            $staffChartData = User::where('divisi_id', 1)->where('role', 'staff')->get()->map(function ($u) use ($allDetailsTAC, $networkDetailsTAC, $start, $diffInDays) {

                // Ambil data Network milik user ini saja
                $uNetworkAll = $networkDetailsTAC->where('dailyReport.user_id', $u->id);
                $uNetworkCase = $uNetworkAll->where('tipe_kegiatan', 'case');
                $uNetworkActivity = $uNetworkAll->where('tipe_kegiatan', 'activity');

                // Karantina GPS user ini
                $uGPS = $allDetailsTAC->where('dailyReport.user_id', $u->id)->where('tipe_kegiatan', 'case')->where('kategori', 'GPS');

                return [
                    'nama' => $u->nama_lengkap,
                    'total_case' => $uNetworkCase->count(),
                    'avg_time' => round($uNetworkCase->avg('value_raw') ?? 0, 1),
                    'net_count' => $uNetworkCase->count(),
                    'net_avg' => round($uNetworkCase->avg('value_raw') ?? 0, 1),
                    'gps_count' => $uGPS->sum('value_raw'),
                    'gps_avg' => 0,
                    'inisiatif_count' => $uNetworkCase->where('temuan_sendiri', 1)->count(),
                    'mandiri_count' => $uNetworkCase->where('is_mandiri', 1)->count(),
                    'activities' => $uNetworkActivity->count(),

                    // Pastikan grafik trend personal juga hanya membaca data Network
                    'daily_history' => $this->getDailyHistory($uNetworkAll, $start, $diffInDays)
                ];
            });
            $leaderboard = $staffChartData->sortByDesc('total_case')->take(5);
        }

        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'summaryData' => $summaryData,
                'workloadMix' => $workloadMix,
                'trend' => ['labels' => $trendLabels, 'cases' => $trendCases, 'activities' => $trendActivities],
                'staffChartData' => $staffChartData,
                'infraTrendData' => $infraTrendData,
                'infraWorkload' => $infraWorkload
            ]);
        }

        return view('manager.dashboard', compact(
            'stats',
            'divisis',
            'selectedDivisi',
            'heatmapData',
            'infraWorkload',
            'infraTrendData',
            'staffWorkloadDist',
            'trendLabels',
            'staffInfraData',
            'availableCategories',
            'staffChartData',
            'workloadMix',
            'leaderboard',
            'trendCases',
            'trendActivities',
            'summaryData'
        ));
    }

    private function getDailyHistory($uDetails, $start, $diffInDays)
    {
        $history = ['cases' => [], 'activities' => []];
        for ($i = 0; $i <= $diffInDays; $i++) {
            $dateStr = (clone $start)->addDays($i)->toDateString();
            $dayData = $uDetails->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr);

            $history['cases'][] = $dayData->where('tipe_kegiatan', 'case')->count();
            $history['activities'][] = $dayData->where('tipe_kegiatan', 'activity')->count();
        }
        return $history;
    }
}
