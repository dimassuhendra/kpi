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

            // 2. Pisahkan Data Case berdasarkan Kategori dengan tegas
            $caseNetwork = $allDetailsTAC->where('tipe_kegiatan', 'case')->where('kategori', 'Network');
            $caseGPS = $allDetailsTAC->where('tipe_kegiatan', 'case')->where('kategori', 'GPS');

            $stats['resolved_month'] = $allDetailsTAC->where('tipe_kegiatan', 'case')->count();

            // PERBAIKAN 1: avg_response_time HANYA DIHITUNG DARI NETWORK
            $stats['avg_response_time'] = $caseNetwork->avg('value_raw') ?? 0;

            $workloadMix = [
                'case' => $stats['resolved_month'],
                'activity' => $allDetailsTAC->where('tipe_kegiatan', 'activity')->count()
            ];

            // Data Summary dengan pemisahan kategori
            $summaryData = [
                'total_case' => $stats['resolved_month'],
                'avg_time' => round($stats['avg_response_time'], 2),
                'network_count' => $caseNetwork->count(),
                'network_avg'   => round($caseNetwork->avg('value_raw') ?? 0, 1),
                'gps_count'     => $caseGPS->count(),
                // 'gps_avg' tidak perlu karena GPS hanya butuh count, tapi jika frontend tetap butuh variabel ini, kirim 0 saja
                'gps_avg'       => 0,
                'mandiri' => $allDetailsTAC->where('tipe_kegiatan', 'case')->where('is_mandiri', 1)->count(),
                'bantuan' => $allDetailsTAC->where('tipe_kegiatan', 'case')->where('is_mandiri', 0)->count(),
                'proaktif' => $allDetailsTAC->where('tipe_kegiatan', 'case')->where('temuan_sendiri', 1)->count(),
                'penugasan' => $allDetailsTAC->where('tipe_kegiatan', 'case')->where('temuan_sendiri', 0)->count(),
            ];

            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $dateStr = $dateObj->toDateString();
                $trendLabels[] = $dateObj->format('d M');

                $dayDetails = $allDetailsTAC->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr);

                $trendCases[] = $dayDetails->where('tipe_kegiatan', 'case')->count();
                $trendActivities[] = $dayDetails->where('tipe_kegiatan', 'activity')->count();
            }

            // Mapping Data Staff 
            $staffChartData = User::where('divisi_id', 1)->where('role', 'staff')->get()->map(function ($u) use ($allDetailsTAC, $start, $diffInDays) {
                $uDetails = $allDetailsTAC->where('dailyReport.user_id', $u->id);

                $uNetwork = $uDetails->where('tipe_kegiatan', 'case')->where('kategori', 'Network');
                $uGPS = $uDetails->where('tipe_kegiatan', 'case')->where('kategori', 'GPS');

                return [
                    'nama' => $u->nama_lengkap,
                    'total_case' => $uDetails->where('tipe_kegiatan', 'case')->count(),

                    // PERBAIKAN 2: avg_time HANYA dari $uNetwork, bukan $uDetails keseluruhan
                    'avg_time' => round($uNetwork->avg('value_raw') ?? 0, 1),

                    'net_count' => $uNetwork->count(),
                    'net_avg' => round($uNetwork->avg('value_raw') ?? 0, 1),
                    'gps_count' => $uGPS->count(),

                    // PERBAIKAN 3: Hapus perhitungan avg_time untuk GPS agar tidak buang resource/salah kaprah
                    'gps_avg' => 0,

                    'inisiatif_count' => $uDetails->where('tipe_kegiatan', 'case')->where('temuan_sendiri', 1)->count(),
                    'mandiri_count' => $uDetails->where('tipe_kegiatan', 'case')->where('is_mandiri', 1)->count(),
                    'daily_history' => $this->getDailyHistory($uDetails, $start, $diffInDays)
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
