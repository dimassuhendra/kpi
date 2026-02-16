<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;
use App\Models\VariabelKpi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{

    public function dashboard(Request $request)
    {
        $selectedDivisi = $request->get('divisi_id', '1');
        $divisis = Divisi::all();

        // Logika Filter Tanggal
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
                // SEKARANG: 7 Hari ke belakang dari hari ini
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

        // Variabel Default
        $infraWorkload = collect();
        $staffInfraData = collect();
        $availableCategories = [];
        $staffChartData = collect();
        $workloadMix = ['case' => 0, 'activity' => 0];
        $trendLabels = [];
        $trendCases = [];
        $trendActivities = [];
        $leaderboard = collect();
        $infraTrendData = [];
        $staffWorkloadDist = collect();
        $summaryData = [
            'total_case' => 0,
            'avg_time' => 0,
            'mandiri' => 0,
            'bantuan' => 0,
            'proaktif' => 0,
            'penugasan' => 0
        ];

        $stats = [
            'pending' => DailyReport::where('status', 'pending')->count(),
            'active_today' => DailyReport::whereDate('tanggal', today())->distinct('user_id')->count('user_id'),
            'resolved_month' => 0,
            'avg_response_time' => 0,
        ];

        $heatmapData = DailyReport::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->whereYear('tanggal', $start->year)
            ->groupBy('date')->pluck('count', 'date');

        if ($selectedDivisi == '2') {
            $allDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end])
                    ->whereHas('user', fn($u) => $u->where('divisi_id', 2));
            })->get();

            $infraWorkload = $allDetails->whereNotNull('kategori')->groupBy('kategori')->map->count();
            $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];

            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $trendLabels[] = $dateObj->format('d M');
                foreach ($availableCategories as $cat) {
                    $infraTrendData[$cat][] = $allDetails->filter(
                        fn($detail) =>
                        $detail->kategori === $cat && Carbon::parse($detail->dailyReport->tanggal)->isSameDay($dateObj)
                    )->count();
                }
            }

            $staffInfraData = User::where('divisi_id', 2)->where('role', 'staff')->get()->map(function ($u) use ($start, $end, $availableCategories) {
                $res = ['nama' => $u->nama_lengkap];
                foreach ($availableCategories as $cat) {
                    $res[$cat] = KegiatanDetail::where('kategori', $cat)
                        ->whereHas('dailyReport', fn($q) => $q->where('user_id', $u->id)->whereBetween('tanggal', [$start, $end]))->count();
                }
                return $res;
            });
            $leaderboard = User::where('role', 'staff')->where('divisi_id', 2)
                ->withCount(['details as total_activity' => fn($q) => $q->whereHas('dailyReport', fn($qr) => $qr->whereBetween('tanggal', [$start, $end]))])
                ->orderByDesc('total_activity')->take(5)->get();
        } elseif ($selectedDivisi == '1') {
            $allDetailsTAC = KegiatanDetail::whereHas('dailyReport', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end])
                    ->whereHas('user', fn($u) => $u->where('divisi_id', 1));
            })->get();

            $stats['resolved_month'] = $allDetailsTAC->where('tipe_kegiatan', 'case')->count();
            $stats['avg_response_time'] = $allDetailsTAC->where('tipe_kegiatan', 'case')->avg('value_raw') ?? 0;

            $workloadMix = [
                'case' => $allDetailsTAC->where('tipe_kegiatan', 'case')->count(),
                'activity' => $allDetailsTAC->where('tipe_kegiatan', 'activity')->count()
            ];

            $diffInDays = $start->diffInDays($end);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $dateObj = (clone $start)->addDays($i);
                $dateStr = $dateObj->toDateString();
                $trendLabels[] = $dateObj->format('d M');
                $trendCases[] = $allDetailsTAC->where('tipe_kegiatan', 'case')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                $trendActivities[] = $allDetailsTAC->where('tipe_kegiatan', 'activity')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
            }

            $summaryData = [
                'total_case' => $stats['resolved_month'],
                'avg_time' => round($stats['avg_response_time'], 2),
                'mandiri' => $allDetailsTAC->where('is_mandiri', 1)->count(),
                'bantuan' => $allDetailsTAC->where('is_mandiri', 0)->where('tipe_kegiatan', 'case')->count(),
                'proaktif' => $allDetailsTAC->where('temuan_sendiri', 1)->count(),
                'penugasan' => $allDetailsTAC->where('temuan_sendiri', 0)->where('tipe_kegiatan', 'case')->count(),
            ];

            $staffChartData = User::where('divisi_id', 1)->where('role', 'staff')->get()->map(function ($u) use ($start, $end, $diffInDays) {
                $uDetails = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $u->id)->whereBetween('tanggal', [$start, $end]))->get();
                $dailyHistory = ['cases' => [], 'activities' => []];
                for ($i = 0; $i <= $diffInDays; $i++) {
                    $dateStr = (clone $start)->addDays($i)->toDateString();
                    $dailyHistory['cases'][] = $uDetails->where('tipe_kegiatan', 'case')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                    $dailyHistory['activities'][] = $uDetails->where('tipe_kegiatan', 'activity')->filter(fn($d) => Carbon::parse($d->dailyReport->tanggal)->toDateString() == $dateStr)->count();
                }
                return [
                    'nama' => $u->nama_lengkap,
                    'total_case' => $uDetails->where('tipe_kegiatan', 'case')->count(),
                    'avg_time' => round($uDetails->where('tipe_kegiatan', 'case')->avg('value_raw') ?? 0, 1),
                    'inisiatif_count' => $uDetails->where('temuan_sendiri', 1)->count(),
                    'mandiri_count' => $uDetails->where('is_mandiri', 1)->count(),
                    'cases' => $uDetails->where('tipe_kegiatan', 'case')->count(),
                    'activities' => $uDetails->where('tipe_kegiatan', 'activity')->count(),
                    'daily_history' => $dailyHistory
                ];
            });
            $leaderboard = $staffChartData->sortByDesc('total_case')->take(5);
        }

        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'summaryData' => $summaryData,
                'workloadMix' => $workloadMix,
                'trend' => ['labels' => $trendLabels, 'cases' => $trendCases, 'activities' => $trendActivities],
                'staffChartData' => $staffChartData,
                'infraTrendData' => $infraTrendData,
                'infraWorkload' => $infraWorkload
            ]);
        }

        return view('manager.dashboard', compact(
            'stats',
            'divisis',
            'selectedDivisi',
            'heatmapData',
            'infraWorkload',
            'infraTrendData',
            'staffWorkloadDist',
            'trendLabels',
            'staffInfraData',
            'availableCategories',
            'staffChartData',
            'workloadMix',
            'leaderboard',
            'trendCases',
            'trendActivities',
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
