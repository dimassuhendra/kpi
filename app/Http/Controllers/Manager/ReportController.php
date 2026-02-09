<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DailyReport;
use App\Exports\KpiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 'staff')->get();
        return view('manager.reports', compact('staffs'));
    }

    public function export(Request $request)
    {
        $filters = [
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        $fileName = 'KPI_Report_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new KpiExport($filters), $fileName);
    }
}
