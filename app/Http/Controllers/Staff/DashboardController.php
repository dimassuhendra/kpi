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
            // Ambil semua detail kegiatan user bulan ini untuk analisa
            $allDetails = KegiatanDetail::with('dailyReport')
                ->whereHas('dailyReport', function ($q) use ($user, $now) {
                    $q->where('user_id', $user->id)
                        ->whereMonth('tanggal', $now->month)
                        ->whereYear('tanggal', $now->year);
                })->get();

            $activityCounts = [];
            $activityDetails = [];
            $wordFrequencies = [];
            $stopWords = ['dan', 'di', 'ke', 'dari', 'yang', 'untuk', 'dengan', 'ini', 'itu', 'pada', 'dalam', 'adalah', 'sebagai', 'tidak', 'akan', 'atau', 'juga', 'bisa', 'ada', 'ya', 'sudah', 'belum', 'saat', 'menjadi', 'karena', 'oleh', 'atas', 'kegiatan', 'aktivitas', 'hari', 'jam', 'menit'];

            foreach ($allDetails as $d) {
                $title = trim($d->nama_kegiatan ?: $d->deskripsi_kegiatan);
                $deskripsiLengkap = strtolower($title . ' ' . ($d->deskripsi_kegiatan ?? ''));

                if (empty($title) || $title == '-') continue;

                // A. Logika Kategori (Sama dengan Manager)
                if (strpos($title, ':') !== false) {
                    $parts = explode(':', $title, 2);
                    $kategori = trim($parts[0]);
                } else {
                    $words = explode(' ', $title);
                    $kategori = $words[0] . (isset($words[1]) && strlen($words[1]) > 3 ? ' ' . $words[1] : '');
                }
                $kategori = strtoupper($kategori);

                if (!isset($activityCounts[$kategori])) {
                    $activityCounts[$kategori] = 0;
                    $activityDetails[$kategori] = [];
                }
                $activityCounts[$kategori]++;
                if (count($activityDetails[$kategori]) < 5) $activityDetails[$kategori][] = $title;

                // B. Logika Word Cloud
                $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $deskripsiLengkap);
                $textWords = explode(' ', $cleanText);
                foreach ($textWords as $word) {
                    $word = trim($word);
                    if (strlen($word) > 2 && !in_array($word, $stopWords) && !is_numeric($word)) {
                        $wordFrequencies[$word] = ($wordFrequencies[$word] ?? 0) + 1;
                    }
                }
            }

            // Top 5 Activities
            arsort($activityCounts);
            $top5 = array_slice($activityCounts, 0, 5, true);
            $chartData['bo']['top_activities_labels'] = array_keys($top5);
            $chartData['bo']['top_activities_series'] = array_values($top5);
            $topDetails = [];
            foreach (array_keys($top5) as $lbl) {
                $topDetails[] = $activityDetails[$lbl];
            }
            $chartData['bo']['top_activities_details'] = $topDetails;

            // Word Cloud
            arsort($wordFrequencies);
            $topWords = array_slice($wordFrequencies, 0, 60, true);
            $wordCloudArray = [];
            foreach ($topWords as $word => $count) {
                $wordCloudArray[] = [$word, $count * 3 + 10];
            }
            $chartData['bo']['wordcloud_data'] = $wordCloudArray;

            // Notulen Briefing (Melihat briefing dari divisi user)
            $chartData['bo']['notulen_slider'] = \App\Models\MeetingNote::with(['user', 'dailyReport'])
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('divisi_id', $user->divisi_id);
                })
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($note) {
                    return [
                        'staff'   => $note->user->nama_lengkap,
                        'judul'   => $note->judul_briefing,
                        'isi'     => $note->isi_notulen,
                        'tanggal' => Carbon::parse($note->dailyReport->tanggal)->translatedFormat('d M Y'),
                        'hari'    => Carbon::parse($note->dailyReport->tanggal)->translatedFormat('l'),
                    ];
                });

            // Trend Volume (7 hari terakhir)
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

            // Personal Timeline Feed (15 terakhir)
            $chartData['bo']['timeline'] = $allDetails->sortByDesc('id')->take(15)->map(function ($item) {
                return [
                    'staff' => 'Me',
                    'judul' => $item->nama_kegiatan ?? $item->judul_kegiatan,
                    'deskripsi' => $item->deskripsi_kegiatan,
                    'waktu' => Carbon::parse($item->created_at)->diffForHumans(),
                    'tanggal' => Carbon::parse($item->dailyReport->tanggal)->format('d M Y')
                ];
            })->values();
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
