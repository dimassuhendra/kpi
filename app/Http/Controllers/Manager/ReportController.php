<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DailyReport;
use App\Exports\KpiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Mengambil staff dikelompokkan berdasarkan divisi agar mudah dipilih di dropdown
        $staffs = User::where('role', 'staff')->with('divisi')->get();
        return view('manager.reports', compact('staffs'));
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
