<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\DailyReport;
use App\Models\KegiatanDetail;

class LogsController extends Controller
{
    public function logs(Request $request)
    {
        $query = DailyReport::with(['user.divisi', 'details']) // Pastikan user.divisi juga di-load
            ->where('user_id', Auth::id());

        // Fitur Search (berdasarkan deskripsi di tabel detail)
        if ($request->has('search')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('deskripsi_kegiatan', 'like', '%' . $request->search . '%');
            });
        }

        // Fitur Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        return view('staff.logs', compact('logs'));
    }

    // Logic Edit Per Case
    public function updateCase(Request $request, $id)
    {
        $detail = KegiatanDetail::where('id', $id)
            ->whereHas('dailyReport', function ($q) {
                $q->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'rejected']);
            })->firstOrFail();

        $detail->update([
            'deskripsi_kegiatan' => $request->deskripsi,
            'value_raw' => $request->respon
        ]);

        return back()->with('success', 'Detail case berhasil diperbarui!');
    }

    // Dummy Export Excel (Contoh sederhana CSV)
    public function exportExcel()
    {
        $logs = DailyReport::with('details')->where('user_id', Auth::id())->get();
        $filename = "Log_Aktivitas_" . date('Ymd') . ".csv";

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Tanggal', 'Deskripsi Case', 'Respon (Min)', 'Tipe', 'Inisiatif']);

        foreach ($logs as $log) {
            foreach ($log->details as $d) {
                fputcsv($handle, [
                    $log->tanggal,
                    $d->deskripsi_kegiatan,
                    $d->value_raw,
                    $d->is_mandiri ? 'Mandiri' : 'Bantuan',
                    $d->temuan_sendiri ? 'Temuan' : 'Laporan'
                ]);
            }
        }

        fclose($handle);
    }

    public function destroy($id)
    {
        $report = DailyReport::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($report->status !== 'pending') {
            return back()->with('error', 'Tidak bisa menghapus laporan yang sudah divalidasi.');
        }

        $report->delete(); // Detail akan otomatis terhapus jika ada On Delete Cascade di DB
        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}
