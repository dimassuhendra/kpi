<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KpiSubmission;
use Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $last30Days = Carbon::now()->subDays(30);

        // 1. Status Hari Ini & Skor Rata-rata (Tetap)
        $todaySubmission = KpiSubmission::where('user_id', $user->id)
            ->whereDate('assessment_date', Carbon::today())
            ->first();

        $averageScore = KpiSubmission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('assessment_date', '>=', $last30Days)
            ->avg('total_final_score') ?? 0;

        // 2. Data Tren 7 Hari (Line Chart)
        $chartData = KpiSubmission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('assessment_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('assessment_date', 'asc')
            ->get(['assessment_date', 'total_final_score']);

        // 3. Data Komposisi Skor per Variabel (Donut Chart)
        // Kita ambil rata-rata nilai per nama variabel untuk user ini
        $variableDistributions = \App\Models\KpiDetail::join('kpi_variables', 'kpi_details.kpi_variable_id', '=', 'kpi_variables.id')
            ->join('kpi_submissions', 'kpi_details.kpi_submission_id', '=', 'kpi_submissions.id')
            ->where('kpi_submissions.user_id', $user->id)
            ->where('kpi_submissions.status', 'approved')
            ->select('kpi_variables.variable_name', \DB::raw('AVG(calculated_score) as avg_val'))
            ->groupBy('kpi_variables.variable_name')
            ->get();

        return view('staff.dashboard', compact(
            'user',
            'todaySubmission',
            'averageScore',
            'chartData',
            'variableDistributions'
        ));
    }
}
