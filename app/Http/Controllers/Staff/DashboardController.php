<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // --- 1. METRIK GLOBAL (Semua Divisi) ---
        // Menggunakan Eloquent agar lebih bersih dan seragam
        $dailyCount = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $now) {
            $q->where('user_id', $user->id)->whereDate('tanggal', $now->toDateString());
        })->count();

        $weeklyCount = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $now) {
            $q->where('user_id', $user->id)->whereBetween('tanggal', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        })->count();

        $monthlyCount = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $now) {
            $q->where('user_id', $user->id)->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year);
        })->count();

        $chartData = [];
        $yesterdayActivities = [];
        $lastReportDate = null;

        // --- 2. LOGIKA DIVISI 1 (TAC) ---
        if ($user->divisi_id == 1) {
            // Ambil data bulan ini untuk rasio
            $monthlyDetails = KegiatanDetail::with('dailyReport')->whereHas('dailyReport', function ($q) use ($user, $now) {
                $q->where('user_id', $user->id)->whereMonth('tanggal', $now->month);
            })->get();

            $cases = $monthlyDetails->where('tipe_kegiatan', 'case');

            $chartData['tac'] = [
                'temuan_vs_laporan' => [
                    $cases->where('temuan_sendiri', 1)->count(),
                    $cases->where('temuan_sendiri', 0)->count()
                ],
                'mandiri_vs_eskalasi' => [
                    $cases->where('is_mandiri', 1)->count(),
                    $cases->where('is_mandiri', 0)->count()
                ],
                'case_vs_activity' => [
                    $cases->count(),
                    $monthlyDetails->where('tipe_kegiatan', 'activity')->count()
                ],
                'avg_response_time' => round($cases->where('temuan_sendiri', 0)->avg('waktu_respon_menit') ?? 0, 1),
            ];

            // Tren 7 Hari Terakhir
            $trendLabels = [];
            $trendCases = [];
            $trendActivities = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $trendLabels[] = $date->format('d M');

                $dayDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $date) {
                    $q->where('user_id', $user->id)->whereDate('tanggal', $date->toDateString());
                })->get();

                $trendCases[] = $dayDetails->where('tipe_kegiatan', 'case')->count();
                $trendActivities[] = $dayDetails->where('tipe_kegiatan', 'activity')->count();
            }

            $chartData['tac']['trend_labels'] = $trendLabels;
            $chartData['tac']['trend_cases'] = $trendCases;
            $chartData['tac']['trend_activities'] = $trendActivities;
        }

        // --- 3. LOGIKA DIVISI 2 (INFRASTRUKTUR) ---
        elseif ($user->divisi_id == 2) {
            $categories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
            $monthlyDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $now) {
                $q->where('user_id', $user->id)->whereMonth('tanggal', $now->month);
            })->get();

            $donut = [];
            foreach ($categories as $cat) {
                $donut[] = $monthlyDetails->where('kategori', $cat)->count();
            }
            $chartData['infra']['categories'] = $categories;
            $chartData['infra']['donut_kategori'] = $donut;

            // Tren 7 Hari Terakhir per Kategori
            $trendLabels = [];
            $trendData = [];
            foreach ($categories as $cat) {
                $trendData[$cat] = [];
            }

            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $trendLabels[] = $date->format('d M');

                $dayDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $date) {
                    $q->where('user_id', $user->id)->whereDate('tanggal', $date->toDateString());
                })->get();

                foreach ($categories as $cat) {
                    $trendData[$cat][] = $dayDetails->where('kategori', $cat)->count();
                }
            }

            $formattedTrend = [];
            foreach ($categories as $cat) {
                $formattedTrend[] = ['name' => $cat, 'data' => $trendData[$cat]];
            }

            $chartData['infra']['trend_labels'] = $trendLabels;
            $chartData['infra']['trend_kategori'] = $formattedTrend;
        }

        // --- 4. LOGIKA DIVISI BACKOFFICE (ID 4 / 3 sesuai db) ---
        else {
            // Ambil tanggal kerja terakhir (sebelum hari ini)
            $lastReportDate = DailyReport::where('user_id', $user->id)
                ->whereDate('tanggal', '<', $now->toDateString())
                ->orderBy('tanggal', 'DESC')
                ->value('tanggal');

            if ($lastReportDate) {
                $yesterdayActivities = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $lastReportDate) {
                    $q->where('user_id', $user->id)->whereDate('tanggal', $lastReportDate);
                })->get();
            }

            // Tren Volume Pekerjaan 7 Hari Terakhir
            $trendLabels = [];
            $trendVolume = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $trendLabels[] = $date->format('d M');
                $trendVolume[] = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $date) {
                    $q->where('user_id', $user->id)->whereDate('tanggal', $date->toDateString());
                })->count();
            }
            $chartData['bo']['trend_labels'] = $trendLabels;
            $chartData['bo']['trend_volume'] = $trendVolume;
        }

        return view('staff.dashboard', compact(
            'dailyCount',
            'weeklyCount',
            'monthlyCount',
            'chartData',
            'yesterdayActivities',
            'lastReportDate'
        ));
    }
}
