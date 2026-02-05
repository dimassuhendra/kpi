<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KpiSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $managerDivId = Auth::user()->division_id;

        // 1. Pending Approval Counter
        $pendingCount = KpiSubmission::whereHas('user', function ($q) use ($managerDivId) {
            $q->where('division_id', $managerDivId);
        })->where('status', 'pending')->count();

        // 2. Statistik Rata-rata Skor Tim (Bulan Ini)
        $avgScore = KpiSubmission::whereHas('user', function ($q) use ($managerDivId) {
            $q->where('division_id', $managerDivId);
        })
            ->where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->avg('total_final_score') ?? 0;

        // 3. Top & Bottom Performer
        $staffPerformers = User::where('division_id', $managerDivId)
            ->where('role', 'staff')
            ->withAvg(['submissions' => function ($q) {
                $q->where('status', 'approved');
            }], 'total_final_score')
            ->get();

        $topPerformer = $staffPerformers->sortByDesc('submissions_avg_total_final_score')->first();
        $bottomPerformer = $staffPerformers->sortBy('submissions_avg_total_final_score')->first();

        // 4. Trend Jumlah Laporan (Submissions) 7 Hari Terakhir
        $trendData = KpiSubmission::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->whereHas('user', function ($q) use ($managerDivId) {
                $q->where('division_id', $managerDivId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return view('manager.dashboard', compact(
            'pendingCount',
            'avgScore',
            'topPerformer',
            'bottomPerformer',
            'trendData'
        ));
    }
}
