<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail; // Tambahkan ini
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedDivisi = $request->get('divisi_id', 'all');
        $viewType = $request->get('view_type', 'all');

        // Pastikan filter hanya untuk TAC (ID: 1 sesuai SQL anda)
        if ($selectedDivisi !== 'all' && $selectedDivisi != 1) {
            return "Modul untuk divisi selain TAC sedang dalam pengembangan.";
        }

        $reportQuery = DailyReport::query();
        $staffQuery = User::where('role', 'staff')->where('divisi_id', 1);

        if ($selectedDivisi !== 'all') {
            $reportQuery->whereHas('user', function ($q) use ($selectedDivisi) {
                $q->where('divisi_id', $selectedDivisi);
            });
        }

        // 1. STATS UTAMA
        $stats = [
            'pending' => (clone $reportQuery)->where('status', 'pending')->count(),
            'avg_kpi' => (clone $reportQuery)->where('status', 'approved')->whereMonth('tanggal', now()->month)->avg('total_nilai_harian') ?? 0,
            'resolved_month' => (clone $reportQuery)->where('status', 'approved')->whereMonth('tanggal', now()->month)->count(),
            'active_today' => (clone $reportQuery)->whereDate('tanggal', today())->distinct('user_id')->count('user_id'),
        ];

        // 2. COLLECTIVE HEATMAP (1 TAHUN)
        $heatmapData = DailyReport::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as count'))
            ->where('tanggal', '>=', now()->startOfYear())
            ->groupBy('date')
            ->pluck('count', 'date');

        // 3. DIAGRAM ANALYTICS (Disesuaikan dengan tabel kegiatan_detail)
        if ($viewType == 'compare') {
            $chartData = User::where('role', 'staff')
                ->where('divisi_id', 1)
                ->withCount(['reports as total_case' => fn($q) => $q->where('status', 'approved')])
                ->withAvg(['reports as avg_response' => fn($q) => $q->where('status', 'approved')], 'total_nilai_harian')
                ->get();
        } else {
            // Kita hitung Mandiri & Inisiatif dari tabel kegiatan_detail
            $chartData = [
                'total_case' => (clone $reportQuery)->where('status', 'approved')->count(),
                'avg_response' => (clone $reportQuery)->where('status', 'approved')->avg('total_nilai_harian') ?? 0,
                // Perbaikan Logic: Ambil dari kegiatan_detail yang reportnya approved
                'mandiri' => KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('status', 'approved'))
                    ->where('is_mandiri', 1)->count(),
                'proaktif' => KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('status', 'approved'))
                    ->where('temuan_sendiri', 1)->count(),
            ];
        }

        $leaderboard = (clone $staffQuery)
            ->withAvg(['reports' => fn($q) => $q->where('status', 'approved')], 'total_nilai_harian')
            ->orderByDesc('reports_avg_total_nilai_harian')->take(5)->get();

        $pendingApprovals = (clone $reportQuery)->with('user')->where('status', 'pending')->latest()->take(5)->get();
        $divisis = Divisi::all();

        return view('manager.dashboard', compact('stats', 'leaderboard', 'pendingApprovals', 'divisis', 'selectedDivisi', 'heatmapData', 'viewType', 'chartData'));
    }

    // ===============================================================
    // Modul Validation
    // ===============================================================
    // ... keep existing dashboard method ...

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
            'status' => 'required|in:approved,rejected',
            'details' => 'required|array'
        ]);

        $report = DailyReport::findOrFail($request->report_id);
        $totalNilai = 0;

        // Update nilai per item kegiatan (Adjust Weight)
        foreach ($request->details as $detailId => $data) {
            $detail = KegiatanDetail::find($detailId);
            if ($detail) {
                $detail->update(['nilai_akhir' => $data['score']]);
                $totalNilai += $data['score'];
            }
        }

        // Update status final laporan
        $report->update([
            'status' => $request->status,
            'total_nilai_harian' => $totalNilai,
            'catatan_manager' => $request->catatan
        ]);

        return redirect()->route('manager.approval.index')->with('success', 'Laporan berhasil diproses.');
    }
}
