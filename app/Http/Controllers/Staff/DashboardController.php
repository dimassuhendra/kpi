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

        // 1. Cek status hari ini
        $todaySubmission = KpiSubmission::where('user_id', $user->id)
            ->whereDate('assessment_date', Carbon::today())
            ->first();

        // 2. Hitung Rata-rata Skor (30 hari terakhir)
        // Jika belum ada data, avg() akan menghasilkan null, kita ubah ke 0
        $averageScore = KpiSubmission::where('user_id', $user->id)
            ->where('status', 'approved') // Hanya yang sudah di-ACC
            ->where('assessment_date', '>=', Carbon::now()->subDays(30))
            ->avg('total_final_score') ?? 0;

        // 3. Ambil data untuk Chart (7 hari terakhir)
        $chartData = KpiSubmission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('assessment_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('assessment_date', 'asc')
            ->get(['assessment_date', 'total_final_score']);

        return view('staff.dashboard', compact('user', 'todaySubmission', 'averageScore', 'chartData'));
    }
}
