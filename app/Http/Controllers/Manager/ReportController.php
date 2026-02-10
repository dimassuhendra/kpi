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
        // Mengambil staff dikelompokkan berdasarkan divisi agar mudah dipilih di dropdown
        $staffs = User::where('role', 'staff')->with('divisi')->get();
        return view('manager.reports', compact('staffs'));
    }

    public function export(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filters = [
            'user_id'    => $request->user_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];

        // Ambil nama staff untuk nama file jika filter user dipilih
        $staffName = 'All_Staff';
        if ($request->user_id) {
            $user = User::find($request->user_id);
            $staffName = str_replace(' ', '_', $user->nama_lengkap);
        }

        $fileName = 'KPI_Analisa_' . $staffName . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new KpiExport($filters), $fileName);
    }
}
