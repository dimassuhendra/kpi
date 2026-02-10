<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;
use App\Models\VariabelKpi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Inisialisasi Parameter Dasar
        $selectedDivisi = $request->get('divisi_id', 'all');
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $divisis = Divisi::all();

        // 2. Inisialisasi Variabel Default (Pencegah Error di Blade)
        $infraWorkload = collect();
        $staffInfraData = collect();
        $workloadMix = collect();
        $trendLabels = [];
        $trendCases = [];
        $trendActivities = [];
        $staffChartData = collect();
        $leaderboard = collect();
        $summaryData = [
            'total_case' => 0,
            'avg_time' => 0,
            'mandiri' => 0,
            'bantuan' => 0,
            'proaktif' => 0,
            'penugasan' => 0
        ];

        // 3. STATS UTAMA & HEATMAP (Muncul di semua tab)
        $stats = [
            'pending' => DailyReport::where('status', 'pending')->count(),
            'active_today' => DailyReport::whereDate('tanggal', today())
                ->distinct('user_id')
                ->count('user_id'),
        ];

        $heatmapData = DailyReport::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->where('tanggal', '>=', now()->startOfYear())
            ->groupBy('date')
            ->pluck('count', 'date');

        // ==========================================
        // LOGIKA PER DIVISI
        // ==========================================

        if ($selectedDivisi == '2') {
            // --- LOGIKA INFRASTRUKTUR ---
            $infraWorkload = KegiatanDetail::whereNotNull('kategori')
                ->whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
                    $q->whereMonth('tanggal', $currentMonth)
                        ->whereYear('tanggal', $currentYear)
                        ->where('status', 'approved');
                })
                ->select('kategori', DB::raw('count(*) as total'))
                ->groupBy('kategori')
                ->pluck('total', 'kategori');

            // Di Controller (Bagian selectedDivisi == '2')
            $staffInfraData = User::where('divisi_id', 2)
                ->where('role', 'staff')
                ->with(['details' => function ($q) use ($currentMonth, $currentYear) {
                    $q->whereHas('dailyReport', function ($qr) use ($currentMonth, $currentYear) {
                        $qr->whereMonth('tanggal', $currentMonth)
                            ->whereYear('tanggal', $currentYear);
                        // Filter status approved sudah dihapus sesuai instruksi Anda
                    });
                }])
                ->get()
                ->map(function ($user) {
                    return [
                        'nama'    => $user->nama_lengkap ?? $user->username,
                        'network' => (int) $user->details->where('kategori', 'Network')->count(),
                        'cctv'    => (int) $user->details->where('kategori', 'CCTV')->count(),
                        'gps'     => (int) $user->details->where('kategori', 'GPS')->count(),
                        'lainnya' => (int) $user->details->where('kategori', 'Lainnya')->count(),
                    ];
                });

            // Pastikan infraWorkload tidak kosong
            if ($infraWorkload->isEmpty()) {
                $infraWorkload = collect(['No Data' => 0]);
            }

            $leaderboard = User::where('role', 'staff')->where('divisi_id', 2)
                ->withCount(['details as total_activity' => function ($q) use ($currentMonth) {
                    $q->whereHas(
                        'dailyReport',
                        fn($qr) =>
                        $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)
                    );
                }])->orderByDesc('total_activity')->take(5)->get();
        } elseif ($selectedDivisi == '1') {
            // --- LOGIKA TAC ---
            $stats['avg_response_time'] = KegiatanDetail::where('tipe_kegiatan', 'case')
                ->whereHas('dailyReport', fn($q) => $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear))
                ->avg('value_raw') ?? 0;

            $stats['resolved_month'] = KegiatanDetail::where('tipe_kegiatan', 'case')
                ->whereHas('dailyReport', fn($q) => $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear))
                ->count();

            $workloadMix = KegiatanDetail::whereHas(
                'dailyReport',
                fn($q) =>
                $q->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'approved')
            )->select('tipe_kegiatan', DB::raw('count(*) as total'))->groupBy('tipe_kegiatan')->pluck('total', 'tipe_kegiatan');

            // Trend Harian
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = now()->setDate($currentYear, $currentMonth, $i)->format('Y-m-d');
                $trendLabels[] = $i;
                $trendCases[] = KegiatanDetail::where('tipe_kegiatan', 'case')->whereHas('dailyReport', fn($q) => $q->where('tanggal', $date)->where('status', 'approved'))->count();
                $trendActivities[] = KegiatanDetail::where('tipe_kegiatan', 'activity')->whereHas('dailyReport', fn($q) => $q->where('tanggal', $date)->where('status', 'approved'))->count();
            }

            $staffChartData = User::where('role', 'staff')->where('divisi_id', 1)
                ->withCount([
                    'details as total_case' => function ($q) use ($currentMonth, $currentYear) {
                        $q->where('tipe_kegiatan', 'case')->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                    },
                    'details as total_activity' => function ($q) use ($currentMonth, $currentYear) {
                        $q->where('tipe_kegiatan', 'activity')->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                    }
                ])->get();

            $mandiri = KegiatanDetail::where('is_mandiri', 1)->whereHas('dailyReport', fn($q) => $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth))->count();
            $proaktif = KegiatanDetail::where('temuan_sendiri', 1)->whereHas('dailyReport', fn($q) => $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth))->count();

            $summaryData = [
                'total_case' => $stats['resolved_month'],
                'avg_time' => $stats['avg_response_time'],
                'mandiri' => $mandiri,
                'bantuan' => $stats['resolved_month'] - $mandiri,
                'proaktif' => $proaktif,
                'penugasan' => $stats['resolved_month'] - $proaktif,
            ];

            $leaderboard = User::where('role', 'staff')->where('divisi_id', 1)
                ->withCount(['details as solved_cases' => function ($q) use ($currentMonth) {
                    $q->where('tipe_kegiatan', 'case')->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth));
                }])->orderByDesc('solved_cases')->take(5)->get();
        } else {
            // --- LOGIKA TAB ALL (SUMMARY SELURUH DIVISI) ---
            // Contoh: Ambil 5 report terbaru dari semua divisi
            $leaderboard = User::where('role', 'staff')
                ->withCount(['details as total_all' => function ($q) use ($currentMonth) {
                    $q->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth));
                }])->orderByDesc('total_all')->take(5)->get();
        }

        // 4. Return Satu View dengan Semua Variabel
        return view('manager.dashboard', compact(
            'stats',
            'divisis',
            'selectedDivisi',
            'heatmapData',
            'infraWorkload',
            'staffInfraData',
            'leaderboard',
            'workloadMix',
            'trendLabels',
            'trendCases',
            'trendActivities',
            'staffChartData',
            'summaryData'
        ));
    }

    // ===============================================================
    // Modul Validation
    // ===============================================================
    public function validationIndex(Request $request)
    {
        $pendingReports = DailyReport::with('user')
            ->where('status', 'pending')
            ->latest('tanggal')
            ->get();

        return view('manager.validation', compact('pendingReports'));
    }

    public function validationShow($id)
    {
        try {
            $report = DailyReport::with(['user', 'details.variabelKpi'])->findOrFail($id);

            $cases = $report->details->where('tipe_kegiatan', 'case');
            $activities = $report->details->where('tipe_kegiatan', 'activity');

            return view('manager.partials.validation-detail', compact('report', 'cases', 'activities'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function validationStore(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:daily_reports,id',
            'status'    => 'required|in:approved,rejected',
            'catatan'   => 'nullable|string'
        ]);

        $report = DailyReport::findOrFail($request->report_id);

        // Update status dan catatan saja, tanpa menyentuh nilai/skor
        $report->update([
            'status'           => $request->status,
            'catatan_manager'  => $request->catatan,
            // Jika kolom nilai di DB belum dihapus, kita bisa set 0 atau biarkan null
            // 'total_nilai_harian' => 0 
        ]);

        $message = $request->status === 'approved'
            ? 'Laporan berhasil disetujui.'
            : 'Laporan telah ditolak untuk revisi.';

        return redirect()->route('manager.approval.index')->with('success', $message);
    }

    // =================================================================
    // Modul KPI Config
    // =================================================================
    public function variablesIndex()
    {
        // Mengambil variabel hanya berdasarkan divisi manager (Divisi 1/TAC)
        $variables = VariabelKpi::with('divisi')
            ->where('divisi_id', 1)
            ->latest()
            ->get();

        $divisis = Divisi::all();
        return view('manager.variables', compact('variables', 'divisis'));
    }

    public function variablesStore(Request $request)
    {
        $request->validate([
            'nama_variabel' => 'required|string|max:255',
            'divisi_id'     => 'required|exists:divisi,id'
        ]);

        // Hanya menyimpan nama_variabel dan divisi_id
        VariabelKpi::create([
            'nama_variabel' => $request->nama_variabel,
            'divisi_id'     => $request->divisi_id,
        ]);

        return back()->with('success', 'Kategori aktivitas berhasil ditambahkan.');
    }

    public function variablesUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_variabel' => 'required|string|max:255',
        ]);

        $variable = VariabelKpi::findOrFail($id);
        $variable->update([
            'nama_variabel' => $request->nama_variabel
        ]);

        return redirect()->route('manager.variables.index')->with('success', 'Kategori aktivitas berhasil diperbarui.');
    }

    public function variablesDestroy($id)
    {
        $variable = VariabelKpi::findOrFail($id);

        // Opsional: Cek apakah variabel ini sudah digunakan di laporan detail
        // Jika sudah digunakan, sebaiknya jangan dihapus (atau gunakan soft delete)

        $variable->delete();

        return back()->with('success', 'Kategori aktivitas berhasil dihapus.');
    }
}
