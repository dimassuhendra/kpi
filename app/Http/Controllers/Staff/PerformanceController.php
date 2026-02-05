<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\KpiSubmission;
use App\Models\KpiCaseLog;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Statistik Utama (30 Hari Terakhir)
        $stats = KpiSubmission::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('assessment_date', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('AVG(total_final_score) as avg_score'),
                DB::raw('COUNT(*) as total_reports')
            )->first();

        // 2. Rata-rata Response Time dari Case Logs
        $avgResponse = KpiCaseLog::whereHas('submission', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('status', 'approved');
        })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->avg('response_time_minutes') ?? 0;

        // 3. Data Chart Tren Performa (15 Hari Terakhir)
        $chartData = KpiSubmission::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('assessment_date', '>=', Carbon::now()->subDays(15))
            ->orderBy('assessment_date', 'asc')
            ->get(['assessment_date', 'total_final_score']);

        return view('staff.performance', compact('stats', 'avgResponse', 'chartData'));
    }
}
