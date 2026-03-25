<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;

class LogsController extends Controller
{
    public function logs(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil Laporan Harian (Kode Lama Anda)
        $query = DailyReport::with(['user.divisi', 'details', 'shift'])
            ->where('user_id', $userId);

        if ($request->has('search')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('deskripsi_kegiatan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // 2. Ambil Riwayat Rating & Kuis khusus user ini
        $feedbacks = CustomerFeedback::where('user_id', $userId)->latest('created_at')->get();
        $assessments = TechnicalAssessment::where('user_id', $userId)->latest('created_at')->get();

        return view('staff.logs', compact('logs', 'feedbacks', 'assessments'));
    }

    // Logic Edit Per Case (Sederhana)
    public function updateCase(Request $request, $id)
    {
        $detail = KegiatanDetail::where('id', $id)
            ->whereHas('dailyReport', function ($q) {
                $q->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'rejected']);
            })->firstOrFail();

        $user = Auth::user();

        // Data dasar yang pasti diupdate semua divisi/kategori
        $updateData = [
            'deskripsi_kegiatan' => $request->deskripsi,
        ];

        if ($user->divisi_id == 1) { // TAC
            if ($detail->kategori == 'Network' && strtolower($detail->deskripsi_kegiatan) !== 'monitoring network') {

                $isTemuan = $request->has('temuan_sendiri');

                // Update Field Teknis
                $updateData['nomor_tiket'] = $request->nomor_tiket;
                $updateData['temuan_sendiri'] = $isTemuan ? 1 : 0;
                $updateData['is_mandiri'] = $request->is_mandiri ?? 1;
                $updateData['pic_name'] = $request->is_mandiri == 0 ? $request->pic_name : null;

                // Logika Image & Respon Time berdasarkan Temuan
                if ($isTemuan) {
                    $updateData['waktu_respon_menit'] = 0;
                    // Jika ada upload bukti baru, timpa yang lama
                    if ($request->hasFile('bukti_deteksi_dini')) {
                        $updateData['bukti_deteksi_dini'] = $request->file('bukti_deteksi_dini')->store('kpi_evidences/deteksi', 'public');
                    }
                } else {
                    $updateData['waktu_respon_menit'] = $request->waktu_respon_menit ?? 0;
                    // Jika ada upload bukti baru, timpa yang lama
                    if ($request->hasFile('bukti_respon_time')) {
                        $updateData['bukti_respon_time'] = $request->file('bukti_respon_time')->store('kpi_evidences/respon', 'public');
                    }
                }
            } elseif ($detail->kategori == 'GPS' && strtolower($detail->deskripsi_kegiatan) !== 'monitoring gps') {
                // Update Kuantitas untuk GPS
                $updateData['value_raw'] = $request->value_raw;
            }
        }

        // Simpan pembaruan ke Database
        $detail->update($updateData);

        return back()->with('success', 'Detail aktivitas berhasil diperbarui secara menyeluruh!');
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
                    $d->waktu_respon_menit,
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
