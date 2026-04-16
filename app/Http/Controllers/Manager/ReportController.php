<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DailyReport;
use App\Exports\KpiExport;
use App\Models\KegiatanDetail;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Tetapkan default tanggal jika tidak ada filter
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Ambil staff beserta relasi divisi
        $staffs = User::where('role', 'staff')
            ->with('divisi')
            ->withCount(['dailyReports' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate])->where('status', 'approved');
            }])
            ->get()
            ->groupBy('divisi.nama_divisi');

        return view('manager.reports', compact('staffs', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filters = [
            'user_id'    => $request->user_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];

        // Ambil nama staff untuk nama file jika filter user dipilih
        $staffName = 'All_Staff';
        if ($request->user_id) {
            $user = User::find($request->user_id);
            $staffName = str_replace(' ', '_', $user->nama_lengkap);
        }

        $fileName = 'KPI_Analisa_' . $staffName . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new KpiExport($filters), $fileName);
    }

    /**
     * Fungsi Export Excel untuk Satu Divisi Penuh
     */
    public function exportDivisi(Request $request)
    {
        $request->validate([
            'divisi'     => 'required',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        // Tangkap dari Blade (nilainya berupa string seperti "Infrastruktur" atau "TAC")
        $namaDivisiDariBlade = $request->divisi;

        // Ubah string menjadi ID. Jika mengandung kata "infra" (huruf besar/kecil bebas), set ID = 2
        $divisiId = 1; // Default TAC
        if (stripos($namaDivisiDariBlade, 'infra') !== false) {
            $divisiId = 2; // Ini kuncinya!
        }

        $filters = [
            'divisi_id'   => $divisiId,
            'divisi_name' => $namaDivisiDariBlade,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ];

        $safeDivisiName = str_replace([' ', '/'], '_', $namaDivisiDariBlade);
        $fileName = 'Report_Divisi_' . $safeDivisiName . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\KpiDivisiExport($filters), $fileName);
    }

    /**
     * Fungsi Export PDF untuk satu User (Laporan Bulanan)
     */
    public function exportPdf(Request $request)
    {
        $user = User::with('divisi')->findOrFail($request->user_id);

        // Ambil tanggal dari request, jika tidak ada baru gunakan bulan ini
        $start = $request->has('start_date') ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $end = $request->has('end_date') ? \Carbon\Carbon::parse($request->end_date) : now()->endOfMonth();

        // 1. Ambil semua detail kegiatan user dalam periode ini
        $allDetails = KegiatanDetail::with('dailyReport')
            ->whereHas('dailyReport', function ($q) use ($user, $start, $end) {
                $q->where('user_id', $user->id)
                    ->whereBetween('tanggal', [$start, $end]);
            })->get();

        // 2. BENTENG FILTER: Buang laporan rutin (Monitoring) agar tidak merusak statistik
        $filteredDetails = $allDetails->filter(function ($item) {
            $desc = strtolower(trim($item->deskripsi_kegiatan));
            $forbiddenWords = ['monitoring gps', 'monitoring network'];
            foreach ($forbiddenWords as $word) {
                if (str_contains($desc, $word)) {
                    return false;
                }
            }
            return true;
        });

        $data = [
            'user' => $user,
            'date' => \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i') . ' WIB',
            'periode' => $start->format('F Y'),
            'divisi_id' => $user->divisi_id,
            'reports' => $filteredDetails->sortByDesc(function ($item) {
                return $item->dailyReport->tanggal;
            }), // Kirim data untuk tabel rincian di bawah
        ];

        if ($user->divisi_id == 2) {
            // --- LOGIKA KHUSUS INFRA ---
            $infraStats = [
                'Network' => $filteredDetails->where('kategori', 'Network')->count(),
                'CCTV'    => $filteredDetails->where('kategori', 'CCTV')->count(),
                'GPS'     => $filteredDetails->where('kategori', 'GPS')->count(),
                'Lainnya' => $filteredDetails->where('kategori', 'Lainnya')->count(),
            ];

            $data['infraStats'] = $infraStats;
            $data['total_case'] = array_sum($infraStats);
        } else {
            // --- LOGIKA KHUSUS TAC (Network & GPS dipisahkan) ---

            // Karantina Data Network
            $netDetails = $filteredDetails->where('kategori', 'Network');
            $netCases = $netDetails->where('tipe_kegiatan', 'case');

            // Karantina Data GPS (Sesuai kesepakatan: Menggunakan SUM)
            $gpsCases = $filteredDetails->where('kategori', 'GPS')->where('tipe_kegiatan', 'case');

            // Sanitasi value_raw untuk GPS (huruf jadi 0)
            $gpsSum = $gpsCases->sum(function ($item) {
                return is_numeric($item->value_raw) ? (float) $item->value_raw : 0;
            });

            $data['tacStats'] = [
                'net_count'      => $netCases->count(),
                'total_activity' => $netDetails->where('tipe_kegiatan', 'activity')->count(),
                'inisiatif_count' => $netCases->where('temuan_sendiri', 1)->count(),
                'mandiri_count'  => $netCases->where('is_mandiri', 1)->count(),
                'avg_time'       => round($netCases->avg('value_raw') ?? 0, 1), // Rata-rata waktu Network
                'gps_count'      => $gpsSum, // Total Unit GPS (Hasil SUM)
            ];
        }

        // Menggunakan ukuran A4 agar tabel rincian muat
        $pdf = Pdf::loadView('manager.exports.user-pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->stream("Performance_{$user->nama_lengkap}.pdf");
    }

    public function preview(Request $request)
    {
        try {
            // Query langsung ke Detail Kegiatan agar sesuai dengan Excel
            $query = \App\Models\KegiatanDetail::with(['dailyReport.user.divisi'])
                ->whereHas('dailyReport', function ($q) use ($request) {
                    $q->where('status', 'approved');

                    if ($request->filled('user_id')) {
                        $q->where('user_id', $request->user_id);
                    }

                    if ($request->filled('start_date')) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    }

                    if ($request->filled('end_date')) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    }
                });

            $details = $query->get();

            $formattedData = $details->map(function ($item) {
                $user = $item->dailyReport->user;
                $divisiId = $user->divisi_id;

                return [
                    'tanggal' => $item->dailyReport->tanggal->format('d/m/Y'),
                    'nama_staff' => $user->nama_lengkap,
                    'divisi' => $divisiId == 1 ? 'TAC' : 'INFRA',
                    // Logika kolom gabungan sesuai Excel
                    'col_5' => $divisiId == 1 ? strtoupper($item->tipe_kegiatan) : ($item->kategori ?? 'Lainnya'),
                    'judul' => $item->deskripsi_kegiatan,
                    'inisiatif' => $divisiId == 1 ? ($item->temuan_sendiri ? 'Ya' : 'Tidak') : '-',
                    'mandiri' => $divisiId == 1 ? ($item->is_mandiri ? 'Ya' : 'Tidak') : '-',
                    'durasi' => $divisiId == 1 ? $item->value_raw : '-',
                ];
            });

            return response()->json($formattedData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id'
        ]);

        try {
            $query = DailyReport::whereBetween('tanggal', [$request->start_date, $request->end_date]);

            // Jika manager memilih staff spesifik, hanya hapus data staff tersebut
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $count = $query->count();

            if ($count === 0) {
                return response()->json(['message' => 'Tidak ada data ditemukan pada periode tersebut.'], 404);
            }

            $query->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus $count laporan beserta detail kegiatannya."
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
