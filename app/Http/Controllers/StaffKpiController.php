<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariabelKpi;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffKpiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        $report = DailyReport::where('user_id', $user->id)
            ->whereDate('tanggal', now()->toDateString())
            ->with('kegiatanDetails')
            ->first();

        $formattedRows = [['deskripsi' => '', 'respons' => '', 'temuan_sendiri' => false, 'is_mandiri' => 1, 'pic_name' => '']];

        if ($report && $report->kegiatanDetails->count() > 0) {
            $formattedRows = $report->kegiatanDetails->map(function ($d) {
                return [
                    'deskripsi' => $d->deskripsi_kegiatan,
                    'respons' => $d->value_raw,
                    'temuan_sendiri' => (bool)$d->temuan_sendiri,
                    'is_mandiri' => $d->is_mandiri ? 1 : 0,
                    'pic_name' => $d->pic_name ?? ''
                ];
            });
        }

        return view('staff.input_kpi', compact('variabelKpis', 'report', 'formattedRows'));    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $report = DailyReport::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => now()->toDateString()],
            ['status' => 'pending']
        );

        $report->kegiatanDetails()->delete();
        $totalPoinHarian = 0;

        // Ambil semua variabel KPI untuk divisi ini agar kita bisa ambil bobotnya
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Mapping ID variabel berdasarkan nama (agar gampang dipanggil)
        $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();
        $vRespons = $variabelKpis->where('nama_variabel', 'Durasi Response (Ambang Batas 15 Menit)')->first();
        $vTemuan = $variabelKpis->where('nama_variabel', 'Case Ditemukan Sendiri')->first();
        $vMandiri = $variabelKpis->where('nama_variabel', 'Penyelesaian Mandiri (Bonus)')->first();

        foreach ($request->case as $item) {
            $poinCaseIni = 0;

            // 1. Poin Dasar per Case (Jika ada variabel Jumlah Case)
            if ($vCount) $poinCaseIni += $vCount->bobot;

            // 2. Poin Respons (Hanya jika bukan temuan sendiri)
            $isTemuan = isset($item['temuan_sendiri']);
            if ($isTemuan) {
                if ($vTemuan) $poinCaseIni += $vTemuan->bobot;
            } else {
                if ($vRespons) {
                    // Logika: < 15 menit full bobot, > 15 menit potong 50%
                    $poinCaseIni += ($item['respons'] <= 15) ? $vRespons->bobot : ($vRespons->bobot * 0.5);
                }
            }

            // 3. Poin Penyelesaian Mandiri
            if ($item['is_mandiri'] == '1') {
                if ($vMandiri) $poinCaseIni += $vMandiri->bobot;
            }

            // Simpan Detail
            KegiatanDetail::create([
                'daily_report_id' => $report->id,
                'variabel_kpi_id' => $vCount->id ?? null, // Kita hubungkan ke variabel utama
                'deskripsi_kegiatan' => $item['deskripsi'] . ($item['is_mandiri'] == '0' ? " (PIC: {$item['pic_name']})" : ""),
                'value_raw' => $item['respons'],
                'nilai_akhir' => $poinCaseIni
            ]);

            $totalPoinHarian += $poinCaseIni;
        }

        $report->update(['total_nilai_harian' => $totalPoinHarian]);

        return redirect()->back()->with('success', 'Laporan berhasil terkirim!');
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Contoh pengambilan data untuk Chart (Hanya count per variabel)
        $stats = \DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->join('variabel_kpi', 'kegiatan_detail.variabel_kpi_id', '=', 'variabel_kpi.id')
            ->where('daily_reports.user_id', $user->id)
            ->select('variabel_kpi.nama_variabel', \DB::raw('count(*) as total'))
            ->groupBy('variabel_kpi.nama_variabel')
            ->get();

        return view('staff.dashboard', compact('stats'));
    }
}
