<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;
use App\Models\LemburReport;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedDivisi = $request->get('divisi_id', '1');
        $divisis = Divisi::all();

        // 1. LOGIKA FILTER TANGGAL
        $filter = $request->get('filter', 'monthly');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        switch ($filter) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case 'weekly':
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case 'custom':
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();
                break;
            default: // monthly
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
        }

        // 2. LOGIKA FILTER STAFF
        $selectedUserId = $request->get('user_id', 'all');

        $staffQuery = User::where('divisi_id', $selectedDivisi)->where('role', 'staff');
        if ($selectedUserId !== 'all') {
            $staffQuery->where('id', $selectedUserId);
        }
        $staffs = $staffQuery->get();
        $staffIds = $staffs->pluck('id')->toArray();

        // 3. AMBIL DATA UTAMA (Berdasarkan filter divisi, tanggal, dan staf)
        $reports = DailyReport::with('details')
            ->whereIn('user_id', $staffIds)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();

        $allDetails = collect();
        foreach ($reports as $report) {
            foreach ($report->details as $detail) {
                // Sisipkan tanggal report ke detail agar mudah ditrending
                $detail->tanggal_report = $report->tanggal;
                $allDetails->push($detail);
            }
        }

        // Label Hari untuk Trend Line/Area Chart
        $trendLabels = [];
        $diffInDays = $start->diffInDays($end);
        for ($i = 0; $i <= $diffInDays; $i++) {
            $trendLabels[] = (clone $start)->addDays($i)->format('Y-m-d');
        }

        // ========================================================================
        // A. METRIK EKSEKUTIF (Semua Divisi - Compliance & Evaluation)
        // ========================================================================
        $gpsReports = $reports->whereNotNull('bukti_report_gps');

        $compliance = [
            'dashboard' => [
                'ontime' => $reports->where('is_dashboard_ontime', 1)->count(),
                'late' => $reports->where('is_dashboard_ontime', 0)->count()
            ],
            'gps' => [
                // Hanya menghitung dari populasi yang ada bukti GPS-nya
                'ontime' => $gpsReports->where('is_gps_ontime', 1)->count(),
                'late' => $gpsReports->where('is_gps_ontime', 0)->count()
            ]
        ];

        // Simulasi/Ambil Evaluasi Teknis Rata-rata Tim (Radar Chart)
        $assessments = TechnicalAssessment::whereIn('user_id', $staffIds)->get();
        $evaluation = [
            'technical' => [
                'network' => $assessments->avg('pemahaman_network') ?? 0,
                'hardware' => $assessments->avg('pemahaman_hardware') ?? 0,
                'software' => $assessments->avg('pemahaman_software') ?? 0,
                'cctv' => $assessments->avg('pemahaman_cctv') ?? 0,
                'gps' => $assessments->avg('pemahaman_gps') ?? 0,
            ],
            // PERBAIKAN: Menggunakan tanggal_survey, COUNT(*) untuk total survey, dan AVG(rating)
            'feedback' => CustomerFeedback::selectRaw('DATE(tanggal_survey) as date, COUNT(*) as total_survey, AVG(rating) as avg_rating')
                ->whereIn('user_id', $staffIds)
                ->whereBetween('tanggal_survey', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->groupBy('date')
                ->orderBy('date', 'asc') // Urutkan dari tanggal terlama agar chart rapi
                ->get()
        ];

        // ========================================================================
        // WADAH CHART DATA
        // ========================================================================
        $chartData = [
            'compliance' => $compliance,
            'evaluation' => $evaluation,
            'trend_labels' => array_map(fn($d) => Carbon::parse($d)->format('d M'), $trendLabels),
            'staff_labels' => $staffs->pluck('nama_lengkap')->toArray(),
            'tac' => [],
            'infra' => [],
            'bo' => []
        ];

        // ========================================================================
        // B. LOGIKA DIVISI 1 (TAC)
        // ========================================================================
        if ($selectedDivisi == '1') {
            // Memfilter data Network yang TIDAK mengandung kata "monitoring"
            $networkCases = $allDetails->where('kategori', 'Network')
                ->where('tipe_kegiatan', 'case')
                ->filter(fn($d) => !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring'));

            // Memfilter data GPS yang TIDAK mengandung kata "monitoring"
            $gpsCases = $allDetails->where('kategori', 'GPS')
                ->where('tipe_kegiatan', 'case')
                ->filter(fn($d) => !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring'));

            // 1. Baris 1: Overview
            $chartData['tac']['temuan_vs_laporan'] = [
                $networkCases->where('temuan_sendiri', 1)->count(), // Temuan
                $networkCases->where('temuan_sendiri', 0)->count()  // Laporan
            ];
            $chartData['tac']['mandiri_vs_eskalasi'] = [
                $networkCases->where('is_mandiri', 1)->count(), // Mandiri
                $networkCases->where('is_mandiri', 0)->count()  // Eskalasi
            ];

            // 2. Baris 2: Rasio & Limit Waktu
            $chartData['tac']['case_vs_activity'] = [
                $allDetails->where('tipe_kegiatan', 'case')->count(),
                $allDetails->where('tipe_kegiatan', 'activity')->count()
            ];
            $chartData['tac']['network_vs_gps'] = [
                $networkCases->count(),
                $gpsCases->count()
            ];

            // Gauge Chart Limit Waktu (Ambil kasus yang BUKAN temuan sendiri karena temuan waktu responnya 0)
            $avgTime = $networkCases->where('temuan_sendiri', 0)->avg('waktu_respon_menit') ?? 0;
            $chartData['tac']['avg_response_time'] = round($avgTime, 1);

            // 3. Baris 3: Kinerja Individu (Leaderboard Staff)
            $barTotalCase = [];
            $barAvgResponse = [];
            $barMandiri = [];
            $barTemuan = [];

            foreach ($staffs as $st) {
                $stNetCases = $networkCases->where('dailyReport.user_id', $st->id);
                $stGpsCases = $gpsCases->where('dailyReport.user_id', $st->id);

                $barTotalCase[] = $stNetCases->count() + $stGpsCases->count();
                $barAvgResponse[] = round($stNetCases->where('temuan_sendiri', 0)->avg('waktu_respon_menit') ?? 0, 1);
                $barMandiri[] = $stNetCases->where('is_mandiri', 1)->count();
                $barTemuan[] = $stNetCases->where('temuan_sendiri', 1)->count();
            }

            $chartData['tac']['staff_total_case'] = $barTotalCase;
            $chartData['tac']['staff_avg_response'] = $barAvgResponse;
            $chartData['tac']['staff_mandiri'] = $barMandiri;
            $chartData['tac']['staff_temuan'] = $barTemuan;

            // 4. Baris 4: Tren Harian (Line & Area)
            $trendActivity = [];
            $trendCase = [];
            $trendQtyGps = [];

            foreach ($trendLabels as $date) {
                // PERBAIKAN: Gunakan Carbon::parse agar format waktunya benar-benar match Y-m-d
                $dayDetails = $allDetails->filter(function ($d) use ($date) {
                    return \Carbon\Carbon::parse($d->tanggal_report)->format('Y-m-d') === $date;
                });

                $trendActivity[] = $dayDetails->where('tipe_kegiatan', 'activity')->count();
                $trendCase[] = $dayDetails->where('tipe_kegiatan', 'case')->count();

                // Area Chart GPS
                $gpsToday = $dayDetails->where('kategori', 'GPS')->where('tipe_kegiatan', 'case');
                $qty = 0;
                foreach ($gpsToday as $g) {
                    $qty += is_numeric($g->value_raw) ? (int)$g->value_raw : 0;
                }
                $trendQtyGps[] = $qty;
            }

            $chartData['tac']['trend_activity'] = $trendActivity;
            $chartData['tac']['trend_case'] = $trendCase;
            $chartData['tac']['trend_qty_gps'] = $trendQtyGps;
        }

        // ========================================================================
        // C. LOGIKA DIVISI 2 (INFRA)
        // ========================================================================
        elseif ($selectedDivisi == '2') {
            $categories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
            $chartData['infra']['categories'] = $categories;

            // 1. Donut Chart (Distribusi Kategori)
            $donutInfra = [];
            foreach ($categories as $cat) {
                $donutInfra[] = $allDetails->where('kategori', $cat)->count();
            }
            $chartData['infra']['donut_kategori'] = $donutInfra;

            // 2. Stacked Bar Chart (Distribusi Kategori per Staf)
            $stackedData = [];
            foreach ($categories as $cat) {
                $dataStaff = [];
                foreach ($staffs as $st) {
                    $dataStaff[] = $allDetails->where('dailyReport.user_id', $st->id)->where('kategori', $cat)->count();
                }
                $stackedData[] = [
                    'name' => $cat,
                    'data' => $dataStaff
                ];
            }
            $chartData['infra']['stacked_staff'] = $stackedData;

            // 3. Line Chart (Tren Harian Kategori)
            $trendInfra = [];
            foreach ($categories as $cat) {
                $dataHari = [];
                foreach ($trendLabels as $date) {
                    // PERBAIKAN: Gunakan Carbon::parse() untuk memastikan format tanggal sama persis
                    $count = $allDetails->filter(function ($d) use ($date, $cat) {
                        return \Carbon\Carbon::parse($d->tanggal_report)->format('Y-m-d') === $date
                            && $d->kategori === $cat;
                    })->count();

                    $dataHari[] = $count;
                }
                $trendInfra[] = [
                    'name' => $cat,
                    'data' => $dataHari
                ];
            }
            $chartData['infra']['trend_kategori'] = $trendInfra;

            // ==========================================
            // TAMBAHAN: LOGIKA CHART LEMBUR (KHUSUS INFRA)
            // ==========================================
            // Ambil data lembur yang sesuai filter tanggal, staff, dan sudah di-approve
            $lemburData = LemburReport::with('dailyReport')
                ->whereHas('dailyReport', function ($q) use ($staffIds, $start, $end) {
                    $q->whereIn('user_id', $staffIds)
                        ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                        ->where('status', 'approved');
                })->get();

            $lemburStaffLabels = [];
            $lemburTotalJam = [];
            $lemburFrekuensi = [];

            foreach ($staffs as $st) {
                $stLembur = $lemburData->filter(function ($l) use ($st) {
                    return $l->dailyReport->user_id == $st->id;
                });

                // Hitung Total Jam (Bar Chart & Doughnut Proporsi Jam)
                $totalMenit = $stLembur->sum(function ($l) {
                    return \Carbon\Carbon::parse($l->waktu_mulai)->diffInMinutes(\Carbon\Carbon::parse($l->waktu_selesai));
                });
                $jam = round($totalMenit / 60, 1);

                // Hitung Frekuensi (Berapa kali lembur)
                $frekuensi = $stLembur->count();

                $lemburStaffLabels[] = $st->nama_lengkap;
                $lemburTotalJam[] = $jam;
                $lemburFrekuensi[] = $frekuensi;
            }

            $chartData['infra']['lembur_labels'] = $lemburStaffLabels;
            $chartData['infra']['lembur_jam'] = $lemburTotalJam;
            $chartData['infra']['lembur_frekuensi'] = $lemburFrekuensi;
        }

        // ========================================================================
        // D. LOGIKA DIVISI BACKOFFICE (BOT, PURCHASING, DLL - ID 3 ke atas)
        // ========================================================================
        elseif ($selectedDivisi >= '3') {
            $activityCounts = [];
            $activityDetails = [];
            $wordFrequencies = [];

            // Daftar stop words (kata yang tidak akan dimasukkan ke wordcloud)
            $stopWords = ['dan', 'di', 'ke', 'dari', 'yang', 'untuk', 'dengan', 'ini', 'itu', 'pada', 'dalam', 'adalah', 'sebagai', 'tidak', 'akan', 'atau', 'juga', 'bisa', 'ada', 'ya', 'sudah', 'belum', 'saat', 'menjadi', 'karena', 'oleh', 'atas', 'kegiatan', 'aktivitas', 'hari', 'jam', 'menit'];

            foreach ($allDetails as $d) {
                $title = trim($d->nama_kegiatan ?: $d->deskripsi_kegiatan);
                $deskripsiLengkap = strtolower($title . ' ' . $d->deskripsi_kegiatan);

                if (empty($title) || $title == '-') continue;

                // 1. LOGIKA TOP 5 KATEGORI
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

                if (!in_array($title, $activityDetails[$kategori]) && count($activityDetails[$kategori]) < 5) {
                    $activityDetails[$kategori][] = $title;
                }

                // 2. LOGIKA WORD CLOUD
                $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $deskripsiLengkap);
                $textWords = explode(' ', $cleanText);

                foreach ($textWords as $word) {
                    $word = trim($word);
                    if (strlen($word) > 2 && !in_array($word, $stopWords) && !is_numeric($word)) {
                        $wordFrequencies[$word] = ($wordFrequencies[$word] ?? 0) + 1;
                    }
                }
            }

            // Simpan Top 5 Kegiatan beserta Detailnya
            arsort($activityCounts);
            $top5Activities = array_slice($activityCounts, 0, 5, true);

            $topLabels = array_keys($top5Activities);
            $topSeries = array_values($top5Activities);

            // Mengambil rincian "list kegiatan" khusus untuk 5 kategori teratas
            $topDetails = [];
            foreach ($topLabels as $lbl) {
                $topDetails[] = $activityDetails[$lbl];
            }

            $chartData['bo']['top_activities_labels'] = $topLabels;
            $chartData['bo']['top_activities_series'] = $topSeries;
            $chartData['bo']['top_activities_details'] = $topDetails; // <-- Data baru untuk Tooltip

            // Simpan Top 60 Kata untuk Word Cloud
            arsort($wordFrequencies);
            $topWords = array_slice($wordFrequencies, 0, 60, true);
            $wordCloudArray = [];
            foreach ($topWords as $word => $count) {
                $wordCloudArray[] = [$word, $count * 3 + 10];
            }
            $chartData['bo']['wordcloud_data'] = $wordCloudArray;

            // 3. Line Chart: Tren Produktivitas Harian (Volume Total)
            $trendVolume = [];
            foreach ($trendLabels as $date) {
                $trendVolume[] = $allDetails->filter(function ($d) use ($date) {
                    return \Carbon\Carbon::parse($d->tanggal_report)->format('Y-m-d') === $date;
                })->count();
            }
            $chartData['bo']['trend_volume'] = $trendVolume;

            // 4. Live Timeline Feed (Ambil 15 aktivitas terbaru)
            $recentActivities = $allDetails->sortByDesc(function ($item) {
                return $item->dailyReport->created_at ?? $item->dailyReport->tanggal;
            })->take(15)->map(function ($item) {
                $judul = $item->nama_kegiatan ?? '';
                $deskripsi = $item->deskripsi_kegiatan ?? '';
                if (empty($judul) && !empty($deskripsi)) {
                    if (strpos($deskripsi, ': ') !== false) {
                        $parts = explode(': ', $deskripsi, 2);
                        $judul = $parts[0];
                        $deskripsi = trim($parts[1]);
                    } else {
                        $judul = $deskripsi;
                        $deskripsi = '';
                    }
                }
                return [
                    'staff' => $item->dailyReport->user->nama_lengkap ?? 'Unknown',
                    'judul' => $judul,
                    'deskripsi' => $deskripsi === '-' ? '' : $deskripsi,
                    'waktu' => \Carbon\Carbon::parse($item->dailyReport->created_at ?? $item->dailyReport->tanggal)->diffForHumans(),
                    'tanggal' => \Carbon\Carbon::parse($item->dailyReport->tanggal)->format('d M Y')
                ];
            })->values();

            $chartData['bo']['timeline'] = $recentActivities;

            // 5. Ambil Notulen Briefing untuk Slider (Sesuai Filter Tanggal & Staff)
            $notulenSlider = \App\Models\MeetingNote::with('user')
                ->whereIn('user_id', $staffIds)
                ->whereHas('dailyReport', function ($q) use ($start, $end) {
                    $q->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'staff' => $note->user->nama_lengkap,
                        'judul' => $note->judul_briefing,
                        'isi' => $note->isi_notulen,
                        'tanggal' => \Carbon\Carbon::parse($note->dailyReport->tanggal)->translatedFormat('d M Y'),
                        'hari' => \Carbon\Carbon::parse($note->dailyReport->tanggal)->translatedFormat('l'),
                    ];
                });

            $chartData['bo']['notulen_slider'] = $notulenSlider;
        }

        // ========================================================================
        // KEMBALIKAN RESPONSE JSON ATAU VIEW
        // ========================================================================
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $chartData
            ]);
        }

        // Jika bukan AJAX, kirim staf untuk dropdown filter dan data awal untuk load chart
        $allStaffs = User::where('divisi_id', $selectedDivisi)->where('role', 'staff')->get();
        return view('manager.dashboard', compact(
            'divisis',
            'selectedDivisi',
            'allStaffs',
            'chartData'
        ));
    }
}
