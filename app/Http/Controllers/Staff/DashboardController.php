<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // --- DATA UMUM ---
        $dailyCount = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->whereDate('daily_reports.tanggal', $now->toDateString())
            ->count();

        $trendData = DB::table('daily_reports')
            ->leftJoin('kegiatan_detail', 'daily_reports.id', '=', 'kegiatan_detail.daily_report_id')
            ->where('daily_reports.user_id', $user->id)
            ->where('daily_reports.tanggal', '>=', $now->copy()->subDays(6)->toDateString())
            ->selectRaw('daily_reports.tanggal, count(kegiatan_detail.id) as total')
            ->groupBy('daily_reports.tanggal')
            ->orderBy('daily_reports.tanggal', 'ASC')
            ->get();

        // Inisialisasi variabel agar tidak error di blade
        $infraWorkload = [];
        $staffInfraData = [];
        $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
        $autonomyData = collect();
        $sourceData = collect();
        $weeklyCount = 0;
        $monthlyCount = 0;
        $lastReportDate = [];
        $yesterdayActivities = [];

        // --- LOGIKA DATA BERDASARKAN ID DIVISI ---
        if ($user->divisi_id == 2) {
            // Tentukan bulan dan tahun saat ini
            $currentMonth = $now->month;
            $currentYear = $now->year;

            $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];

            // 1. Workload Distribution (HANYA BULAN INI)
            $infraWorkload = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $currentMonth) // Filter Bulan
                ->whereYear('daily_reports.tanggal', $currentYear)   // Filter Tahun
                ->selectRaw('kategori, count(*) as total')
                ->groupBy('kategori')
                ->pluck('total', 'kategori')->toArray();

            // 2. Staff Technical Focus (HANYA BULAN INI)
            $staffInfraData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $currentMonth) // Filter Bulan
                ->whereYear('daily_reports.tanggal', $currentYear)   // Filter Tahun
                ->selectRaw('daily_reports.tanggal as tgl, kategori, count(*) as total')
                ->groupBy('daily_reports.tanggal', 'kategori')
                ->orderBy('daily_reports.tanggal', 'ASC') // Urutkan dari tanggal awal bulan
                ->get()
                ->groupBy('tgl')
                ->map(function ($items, $date) use ($availableCategories) {
                    $res = ['nama' => date('d M', strtotime($date))];
                    foreach ($availableCategories as $cat) {
                        $res[$cat] = $items->where('kategori', $cat)->first()->total ?? 0;
                    }
                    return $res;
                })->values();
        } elseif ($user->divisi_id == 1) {
            // DIVISI TAC (Default)
            $weeklyCount = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereBetween('daily_reports.tanggal', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()])
                ->count();

            $monthlyCount = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $now->month)
                ->count();

            $autonomyData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->selectRaw('is_mandiri, count(*) as total')
                ->groupBy('is_mandiri')->get();

            $sourceData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->selectRaw('temuan_sendiri, count(*) as total')
                ->groupBy('temuan_sendiri')->get();

        } elseif ($user->divisi_id == 6) {
            // ==========================================
            // LOGIKA BACKOFFICE (SEDERHANA)
            // ==========================================

            // 1. Hitung total aktivitas minggu ini & bulan ini
            $weeklyCount = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereBetween('daily_reports.tanggal', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()])
                ->count();

            $monthlyCount = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $now->month)
                ->count();

            // 2. Ambil Daftar Pekerjaan Kemarin (Last Working Day)
            // Kita cari tanggal laporan terakhir sebelum hari ini
            $lastReportDate = DB::table('daily_reports')
                ->where('user_id', $user->id)
                ->where('tanggal', '<', $now->toDateString())
                ->orderBy('tanggal', 'DESC')
                ->value('tanggal');

            $yesterdayActivities = [];
            if ($lastReportDate) {
                $yesterdayActivities = DB::table('kegiatan_detail')
                    ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                    ->where('daily_reports.user_id', $user->id)
                    ->where('daily_reports.tanggal', $lastReportDate)
                    ->select('kegiatan_detail.*', 'daily_reports.tanggal')
                    ->get();
            }
        }

        // Kirim ke SATU file blade yang sama
        return view('staff.dashboard', compact(
            'dailyCount',
            'trendData',
            'infraWorkload',
            'staffInfraData',
            'availableCategories',
            'weeklyCount',
            'monthlyCount',
            'autonomyData',
            'sourceData',
            'lastReportDate',
            'yesterdayActivities'
        ));
    }
}
