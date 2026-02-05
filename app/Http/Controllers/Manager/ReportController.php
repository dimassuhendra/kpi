<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KpiSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $divisionId = Auth::user()->division_id;

        $reportData = $this->getMonthlyData($month, $year, $divisionId);

        return view('manager.reports', compact('reportData', 'month', 'year'));
    }

    private function getMonthlyData($month, $year, $divId)
    {
        return User::where('division_id', $divId)
            ->where('role', 'staff')
            ->with(['kpiSubmissions' => function ($q) use ($month, $year) {
                $q->whereMonth('assessment_date', $month)
                    ->whereYear('assessment_date', $year)
                    ->where('status', 'approved');
            }])
            ->get()
            ->map(function ($user) {
                $submissions = $user->kpiSubmissions;
                return [
                    'name' => $user->name,
                    'total_reports' => $submissions->count(),
                    'avg_score' => $submissions->avg('total_final_score') ?? 0,
                    'on_time_count' => $submissions->where('is_on_time', 1)->count(),
                    'late_count' => $submissions->where('is_on_time', 0)->count(),
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
