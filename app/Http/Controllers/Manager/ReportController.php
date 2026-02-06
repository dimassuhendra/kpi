<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KpiSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $divisionId = Auth::user()->division_id;

        $reportData = $this->getMonthlyData($month, $year, $divisionId);

        // Data Tambahan untuk Chart Perbandingan Antar Staff
        $comparisonData = [
            'labels' => $reportData->pluck('name'),
            'scores' => $reportData->pluck('avg_score'),
            'cases' => $reportData->pluck('total_cases')
        ];

        return view('manager.reports', compact('reportData', 'month', 'year', 'comparisonData'));
    }

    private function getMonthlyData($month, $year, $divId)
    {
        return User::where('division_id', $divId)
            ->where('role', 'staff')
            ->with(['kpiSubmissions' => function ($q) use ($month, $year) {
                $q->whereMonth('assessment_date', $month)
                    ->whereYear('assessment_date', $year)
                    ->where('status', 'approved')
                    ->with(['details.variable', 'caseLogs']);
            }])
            ->get()
            ->map(function ($user) {
                $submissions = $user->kpiSubmissions;

                // Agregasi Case Logs dari seluruh submission bulan ini
                $allCaseLogs = $submissions->flatMap->caseLogs;
                $allDetails = $submissions->flatMap->details;

                // Hitung Rata-rata per variabel untuk Chart
                $variableStats = $allDetails->groupBy('kpi_variable_id')->map(function ($group) {
                    return [
                        'name' => $group->first()->variable->variable_name ?? 'Unknown',
                        'avg_score' => $group->avg('calculated_score'),
                        // Menghitung skor murni (0-100) sebelum dikali bobot
                        'base_score' => $group->avg(function ($d) {
                            return ($d->variable->weight > 0) ? ($d->calculated_score / ($d->variable->weight / 100)) : 0;
                        })
                    ];
                })->values();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_reports' => $submissions->count(),
                    'avg_score' => $submissions->avg('total_final_score') ?? 0,
                    'on_time_count' => $submissions->where('manager_correction', '1')->count(), // Berdasarkan input manager
                    'late_count' => $submissions->where('manager_correction', '0')->count(),

                    // Statistik Tambahan untuk View & Chart
                    'total_cases' => $allCaseLogs->count(),
                    'avg_response' => round($allCaseLogs->avg('response_time_minutes') ?? 0, 1),
                    'self_detected_cases' => $allCaseLogs->where('is_problem_detected_by_staff', 1)->count(),
                    'chart_labels' => $variableStats->pluck('name'),
                    'chart_values' => $variableStats->pluck('base_score'),
                ];
            });
    }

    public function exportExcel(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $data = $this->getMonthlyData($month, $year, Auth::user()->division_id);

        $fileName = "KPI_Report_{$month}_{$year}.xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        echo view('manager.reports_export', compact('data', 'month', 'year'));
        exit;
    }

    public function exportPdf(Request $request)
    {
        // Untuk PDF Profesional biasanya menggunakan DomPDF, 
        $month = $request->month;
        $year = $request->year;
        $data = $this->getMonthlyData($month, $year, Auth::user()->division_id);

        return view('manager.reports_export', compact('data', 'month', 'year'))->with('isPdf', true);
    }
}
