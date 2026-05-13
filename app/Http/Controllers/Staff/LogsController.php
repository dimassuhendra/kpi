<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;

use App\Exports\KpiExport;
use Maatwebsite\Excel\Facades\Excel;

class LogsController extends Controller
{
    public function logs(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil Laporan Harian & Lembur
        $query = DailyReport::with(['user.divisi', 'details', 'shift', 'lemburReport', 'meetingNote'])
            ->where('user_id', $userId);

        // Filter Pencarian Deskripsi
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('deskripsi_kegiatan', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- TAMBAHKAN FILTER TANGGAL DI SINI ---
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        // ----------------------------------------

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

        // Validasi input form dan file gambar
        $request->validate([
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bukti_respon_time' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bukti_deteksi_dini' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Data dasar yang pasti diupdate semua divisi/kategori
        $updateData = [
            'deskripsi_kegiatan' => $request->deskripsi,
        ];

        // ==========================================
        // LOGIKA KHUSUS TAC (DIVISI 1)
        // ==========================================
        if ($user->divisi_id == 1) {
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
                        if ($detail->bukti_deteksi_dini) {
                            Storage::disk('public')->delete($detail->bukti_deteksi_dini);
                        }
                        $updateData['bukti_deteksi_dini'] = $request->file('bukti_deteksi_dini')->store('kpi_evidences/deteksi', 'public');
                    }
                } else {
                    $updateData['waktu_respon_menit'] = $request->waktu_respon_menit ?? 0;
                    // Jika ada upload bukti baru, timpa yang lama
                    if ($request->hasFile('bukti_respon_time')) {
                        if ($detail->bukti_respon_time) {
                            Storage::disk('public')->delete($detail->bukti_respon_time);
                        }
                        $updateData['bukti_respon_time'] = $request->file('bukti_respon_time')->store('kpi_evidences/respon', 'public');
                    }
                }
            } elseif ($detail->kategori == 'GPS' && strtolower($detail->deskripsi_kegiatan) !== 'monitoring gps') {
                // Update Kuantitas untuk GPS
                $updateData['value_raw'] = $request->value_raw;
            }
        }
        // ==========================================
        // LOGIKA KHUSUS INFRASTRUKTUR (DIVISI 2)
        // ==========================================
        elseif ($user->divisi_id == 2) {
            // Update Kategori
            if ($request->has('kategori')) {
                $updateData['kategori'] = $request->kategori;
            }

            // Jika ada upload foto dokumentasi baru, timpa yang lama
            if ($request->hasFile('foto_dokumentasi')) {
                if ($detail->foto_dokumentasi) {
                    Storage::disk('public')->delete($detail->foto_dokumentasi);
                }
                $updateData['foto_dokumentasi'] = $request->file('foto_dokumentasi')->store('kpi_evidences/infra', 'public');
            }
        }

        // Simpan pembaruan ke Database
        $detail->update($updateData);

        return back()->with('success', 'Detail aktivitas berhasil diperbarui secara menyeluruh!');
    }

    // Logika Hapus Per Case / Item Kegiatan
    public function destroyCase($id)
    {
        // Cari detail kegiatan dan pastikan laporannya milik user yang sedang login 
        // serta statusnya masih pending atau rejected
        $detail = KegiatanDetail::where('id', $id)
            ->whereHas('dailyReport', function ($q) {
                $q->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'rejected']);
            })->firstOrFail();

        $reportId = $detail->daily_report_id; // Simpan ID DailyReport (sesuaikan nama kolom foreign key jika berbeda)

        // Hapus file bukti (gambar) dari storage jika ada
        if ($detail->bukti_deteksi_dini) {
            Storage::disk('public')->delete($detail->bukti_deteksi_dini);
        }
        if ($detail->bukti_respon_time) {
            Storage::disk('public')->delete($detail->bukti_respon_time);
        }
        if ($detail->foto_dokumentasi) { // Tambahan hapus gambar infra
            Storage::disk('public')->delete($detail->foto_dokumentasi);
        }

        // Hapus item detail
        $detail->delete();

        // Cek apakah ini adalah item terakhir di DailyReport tersebut
        $remainingDetails = KegiatanDetail::where('daily_report_id', $reportId)->count();

        if ($remainingDetails === 0) {
            // Jika tidak ada item lagi, sekalian hapus Daily Report-nya
            DailyReport::find($reportId)->delete();
            return back()->with('success', 'Item dihapus. Karena tidak ada kegiatan tersisa, laporan harian otomatis dihapus.');
        }

        return back()->with('success', 'Item kegiatan berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();

        // KUNCI UTAMA: Paksa user_id ke ID milik staff yang sedang login
        $filters = [
            'user_id'    => $user->id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'divisi_id' => $request->divisi_id,
        ];

        // Buat nama file
        $staffName = str_replace(' ', '_', $user->nama_lengkap);
        $fileName = 'KPI_Analisa_Saya_' . $staffName . '_' . now()->format('Ymd_His') . '.xlsx';

        // Panggil class KpiExport yang sama persis seperti di Manager
        return Excel::download(new KpiExport($filters), $fileName);
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
