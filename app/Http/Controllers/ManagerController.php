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
        $report = DailyReport::findOrFail($request->report_id);

        $totalPoinDapat = 0;
        $totalBobotMaksimal = 0;

        foreach ($request->details as $detailId => $data) {
            $detail = KegiatanDetail::with('variabelKpi')->find($detailId);

            if ($detail && $detail->variabelKpi) {
                $skorInput = $data['score']; // Manager input 0 - 5
                $bobotVariabel = $detail->variabelKpi->bobot;

                // Hitung poin yang didapat untuk variabel ini
                // (Skor / 5) * Bobot
                $poinVariabel = ($skorInput / 5) * $bobotVariabel;

                $detail->update([
                    'nilai_akhir' => $poinVariabel
                ]);

                $totalPoinDapat += $poinVariabel;
                $totalBobotMaksimal += $bobotVariabel;
            }
        }

        // NORMALISASI KE 100
        // Jadi mau total bobot lu 135 atau 1000 pun, hasilnya tetep skala 100
        $nilaiFinal = ($totalBobotMaksimal > 0) ? ($totalPoinDapat / $totalBobotMaksimal) * 100 : 0;

        $report->update([
            'status' => $request->status,
            'total_nilai_harian' => $nilaiFinal,
            'catatan_manager' => $request->catatan
        ]);

        return redirect()->route('manager.approval.index')->with('success', 'Validasi Berhasil. Skor Akhir: ' . number_format($nilaiFinal, 1));
    }

    // =================================================================
    // Modul KPI Config
    // =================================================================
    public function variablesIndex()
    {
        // Menggunakan pagination lebih disarankan jika data mulai banyak
        $variables = VariabelKpi::with('divisi')->where('divisi_id', 1)->get();
        $divisis = Divisi::all();
        return view('manager.variables', compact('variables', 'divisis'));
    }

    public function variablesStore(Request $request)
    {
        $request->validate([
            'nama_variabel' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0', // Tambah validasi minimal 0
            'divisi_id' => 'required|exists:divisi,id'
        ]);

        VariabelKpi::create($request->all());
        return back()->with('success', 'Variabel KPI berhasil ditambahkan.');
    }

    // Ubah $id menjadi $variable agar sinkron dengan Laravel Resource
    public function variablesUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_variabel' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0',
        ]);

        $variable = VariabelKpi::findOrFail($id);
        $variable->update($request->all());

        return redirect()->route('manager.variables.index')->with('success', 'Variabel berhasil diperbarui.');
    }

    public function variablesDestroy($id)
    {
        $variable = VariabelKpi::findOrFail($id);
        $variable->delete();

        return back()->with('success', 'Variabel berhasil dihapus.');
    }
}
