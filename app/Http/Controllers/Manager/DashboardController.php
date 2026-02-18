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
                // SEKARANG: 7 Hari ke belakang dari hari ini
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

        if ($selectedDivisi == '2') {
            $allDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($start, $end) {
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

            $staffInfraData = User::where('divisi_id', 2)->where('role', 'staff')->get()->map(function ($u) use ($start, $end, $availableCategories) {
                $res = ['nama' => $u->nama_lengkap];
                foreach ($availableCategories as $cat) {
                    $res[$cat] = KegiatanDetail::where('kategori', $cat)
                        ->whereHas('dailyReport', fn($q) => $q->where('user_id', $u->id)->whereBetween('tanggal', [$start, $end]))->count();
                }
                return $res;
            });
            $leaderboard = User::where('role', 'staff')->where('divisi_id', 2)
                ->withCount(['details as total_activity' => fn($q) => $q->whereHas('dailyReport', fn($qr) => $qr->whereBetween('tanggal', [$start, $end]))])
                ->orderByDesc('total_activity')->take(5)->get();
        } elseif ($selectedDivisi == '1') {
            $allDetailsTAC = KegiatanDetail::whereHas('dailyReport', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end])
                    ->whereHas('user', fn($u) => $u->where('divisi_id', 1));
            })->get();

            $stats['resolved_month'] = $allDetailsTAC->where('tipe_kegiatan', 'case')->count();
            $stats['avg_response_time'] = $allDetailsTAC->where('tipe_kegiatan', 'case')->avg('value_raw') ?? 0;

            $workloadMix = [
                'case' => $allDetailsTAC->where('tipe_kegiatan', 'case')->count(),
                'activity' => $allDetailsTAC->where('tipe_kegiatan', 'activity')->count()
            ];

            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $dateStr = $dateObj->toDateString();
                $trendLabels[] = $dateObj->format('d M');
                $trendCases[] = $allDetailsTAC->where('tipe_kegiatan', 'case')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                $trendActivities[] = $allDetailsTAC->where('tipe_kegiatan', 'activity')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
            }

            $summaryData = [
                'total_case' => $stats['resolved_month'],
                'avg_time' => round($stats['avg_response_time'], 2),
                'mandiri' => $allDetailsTAC->where('is_mandiri', 1)->count(),
                'bantuan' => $allDetailsTAC->where('is_mandiri', 0)->where('tipe_kegiatan', 'case')->count(),
                'proaktif' => $allDetailsTAC->where('temuan_sendiri', 1)->count(),
                'penugasan' => $allDetailsTAC->where('temuan_sendiri', 0)->where('tipe_kegiatan', 'case')->count(),
            ];

            $staffChartData = User::where('divisi_id', 1)->where('role', 'staff')->get()->map(function ($u) use ($start, $end, $diffInDays) {
                $uDetails = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $u->id)->whereBetween('tanggal', [$start, $end]))->get();
                $dailyHistory = ['cases' => [], 'activities' => []];
                for ($i = 0; $i <= $diffInDays; $i++) {
                    $dateStr = (clone $start)->addDays($i)->toDateString();
                    $dailyHistory['cases'][] = $uDetails->where('tipe_kegiatan', 'case')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                    $dailyHistory['activities'][] = $uDetails->where('tipe_kegiatan', 'activity')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                }
                return [
                    'nama' => $u->nama_lengkap,
                    'total_case' => $uDetails->where('tipe_kegiatan', 'case')->count(),
                    'avg_time' => round($uDetails->where('tipe_kegiatan', 'case')->avg('value_raw') ?? 0, 1),
                    'inisiatif_count' => $uDetails->where('temuan_sendiri', 1)->count(),
                    'mandiri_count' => $uDetails->where('is_mandiri', 1)->count(),
                    'cases' => $uDetails->where('tipe_kegiatan', 'case')->count(),
                    'activities' => $uDetails->where('tipe_kegiatan', 'activity')->count(),
                    'daily_history' => $dailyHistory
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
}
