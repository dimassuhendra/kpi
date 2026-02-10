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
        $selectedDivisi = $request->get('divisi_id', 'all');

        // Filter TAC (ID: 1) - Sesuaikan jika ada divisi lain nantinya
        if ($selectedDivisi !== 'all' && $selectedDivisi != 1) {
            return "Modul untuk divisi selain TAC sedang dalam pengembangan.";
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // --- 1. STATS UTAMA (KPI Dasar) ---
        $stats = [
            'pending' => DailyReport::where('status', 'pending')->count(),
            'avg_response_time' => KegiatanDetail::where('tipe_kegiatan', 'case')
                ->whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
                    $q->where('status', 'approved')
                        ->whereMonth('tanggal', $currentMonth)
                        ->whereYear('tanggal', $currentYear);
                })->avg('value_raw') ?? 0,
            'resolved_month' => KegiatanDetail::where('tipe_kegiatan', 'case')
                ->whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
                    $q->where('status', 'approved')
                        ->whereMonth('tanggal', $currentMonth)
                        ->whereYear('tanggal', $currentYear);
                })->count(),
            'active_today' => DailyReport::whereDate('tanggal', today())->distinct('user_id')->count('user_id'),
        ];

        // --- 2. HEATMAP (Aktivitas Login/Submit Report) ---
        $heatmapData = DailyReport::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->where('tanggal', '>=', now()->startOfYear())
            ->groupBy('date')
            ->pluck('count', 'date');

        // --- 3. DATA PRODUCTIVITY MIX (Donut Chart: Case vs Activity) ---
        $workloadMix = KegiatanDetail::whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
            $q->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'approved');
        })
            ->select('tipe_kegiatan', DB::raw('count(*) as total'))
            ->groupBy('tipe_kegiatan')
            ->pluck('total', 'tipe_kegiatan');

        // --- 4. DATA TREND HARIAN (Line Chart: Case vs Activity) ---
        $daysInMonth = now()->daysInMonth;
        $trendLabels = [];
        $trendCases = [];
        $trendActivities = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = now()->setDate($currentYear, $currentMonth, $i)->format('Y-m-d');
            $trendLabels[] = $i;

            $trendCases[] = KegiatanDetail::where('tipe_kegiatan', 'case')
                ->whereHas('dailyReport', function ($q) use ($date) {
                    $q->where('tanggal', $date)->where('status', 'approved');
                })->count();

            $trendActivities[] = KegiatanDetail::where('tipe_kegiatan', 'activity')
                ->whereHas('dailyReport', function ($q) use ($date) {
                    $q->where('tanggal', $date)->where('status', 'approved');
                })->count();
        }

        // --- 5. ANALYTICS PER STAFF (Bar Charts & Stacked Bar) ---
        $staffChartData = User::where('role', 'staff')
            ->where('divisi_id', 1)
            ->withCount([
                // Total Case per orang
                'details as total_case' => function ($q) use ($currentMonth, $currentYear) {
                    $q->where('tipe_kegiatan', 'case')
                        ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                },
                // Total Activity per orang
                'details as total_activity' => function ($q) use ($currentMonth, $currentYear) {
                    $q->where('tipe_kegiatan', 'activity')
                        ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                },
                'details as mandiri_count' => function ($q) use ($currentMonth, $currentYear) {
                    $q->where('is_mandiri', 1)->where('tipe_kegiatan', 'case')
                        ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                },
                'details as inisiatif_count' => function ($q) use ($currentMonth, $currentYear) {
                    $q->where('temuan_sendiri', 1)->where('tipe_kegiatan', 'case')
                        ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
                }
            ])
            ->withAvg(['details as avg_time' => function ($q) use ($currentMonth, $currentYear) {
                $q->where('tipe_kegiatan', 'case')
                    ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear));
            }], 'value_raw')
            ->get();

        // --- 6. SUMMARY DIVISI (Untuk Donut KPI) ---
        $mandiriCount = KegiatanDetail::where('is_mandiri', 1)->where('tipe_kegiatan', 'case')
            ->whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
                $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear);
            })->count();

        $inisiatifCount = KegiatanDetail::where('temuan_sendiri', 1)->where('tipe_kegiatan', 'case')
            ->whereHas('dailyReport', function ($q) use ($currentMonth, $currentYear) {
                $q->where('status', 'approved')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear);
            })->count();

        $summaryData = [
            'total_case' => $stats['resolved_month'],
            'avg_time'   => $stats['avg_response_time'],
            'mandiri'    => $mandiriCount,
            'bantuan'    => $stats['resolved_month'] - $mandiriCount,
            'proaktif'   => $inisiatifCount,
            'penugasan'  => $stats['resolved_month'] - $inisiatifCount,
        ];

        // --- 7. LEADERBOARD ---
        $leaderboard = User::where('role', 'staff')
            ->where('divisi_id', 1)
            ->withCount(['details as solved_cases' => function ($q) use ($currentMonth) {
                $q->where('tipe_kegiatan', 'case')
                    ->whereHas('dailyReport', fn($qr) => $qr->where('status', 'approved')->whereMonth('tanggal', $currentMonth));
            }])
            ->orderByDesc('solved_cases')
            ->take(5)
            ->get();

        $divisis = Divisi::all();

        return view('manager.dashboard', compact(
            'stats',
            'leaderboard',
            'divisis',
            'selectedDivisi',
            'heatmapData',
            'staffChartData',
            'summaryData',
            'workloadMix',
            'trendLabels',
            'trendCases',
            'trendActivities'
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
        $report = DailyReport::with(['user', 'details.variabelKpi'])->findOrFail($id);

        // Mengembalikan partial view untuk ditampilkan di sisi kanan (AJAX)
        return view('manager.partials.validation-detail', compact('report'))->render();
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
