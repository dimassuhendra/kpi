<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\VariabelKpi;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;

class StaffKpiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Selalu mulai dengan form kosong setiap kali halaman dibuka
        $formattedRows = [[
            'deskripsi' => '',
            'respons' => '',
            'temuan_sendiri' => false,
            'is_mandiri' => 1,
            'pic_name' => ''
        ]];

        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Buat report baru setiap kali submit (setiap baris dianggap satu laporan final)
        // Jika ingin menggabungkan dalam satu laporan harian, kita gunakan updateOrCreate
        $report = DailyReport::create([
            'user_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $totalPoinHarian = 0;
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();
        $vRespons = $variabelKpis->where('nama_variabel', 'Durasi Response (Ambang Batas 15 Menit)')->first();
        $vTemuan = $variabelKpis->where('nama_variabel', 'Case Ditemukan Sendiri')->first();
        $vMandiri = $variabelKpis->where('nama_variabel', 'Penyelesaian Mandiri (Bonus)')->first();

        foreach ($request->case as $item) {
            $poinCaseIni = 0;
            if ($vCount) $poinCaseIni += $vCount->bobot;

            $isTemuan = isset($item['temuan_sendiri']);
            if ($isTemuan) {
                if ($vTemuan) $poinCaseIni += $vTemuan->bobot;
            } else {
                if ($vRespons) {
                    $poinCaseIni += (($item['respons'] ?? 0) <= 15) ? $vRespons->bobot : ($vRespons->bobot * 0.5);
                }
            }

            if (($item['is_mandiri'] ?? '1') == '1') {
                if ($vMandiri) $poinCaseIni += $vMandiri->bobot;
            }

            KegiatanDetail::create([
                'daily_report_id' => $report->id,
                'variabel_kpi_id' => $vCount->id ?? null,
                'deskripsi_kegiatan' => $item['deskripsi'],
                'value_raw' => $item['respons'] ?? 0,
                'temuan_sendiri' => $isTemuan ? 1 : 0,
                'is_mandiri' => $item['is_mandiri'] ?? 1,
                'pic_name' => ($item['is_mandiri'] ?? '1') == '0' ? ($item['pic_name'] ?? '') : null,
                'nilai_akhir' => $poinCaseIni
            ]);

            $totalPoinHarian += $poinCaseIni;
        }

        $report->update(['total_nilai_harian' => $totalPoinHarian]);

        return redirect()->route('staff.input')->with('success', 'Laporan Berhasil Disimpan!');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Hitung Total Case (Daily, Weekly, Monthly)
        $dailyCount = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->whereDate('daily_reports.tanggal', $now->toDateString())
            ->count();

        $weeklyCount = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->whereBetween('daily_reports.tanggal', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()])
            ->count();

        $monthlyCount = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->whereMonth('daily_reports.tanggal', $now->month)
            ->count();

        // 2. Data Doughnut: Mandiri vs Bantuan
        $autonomyData = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->selectRaw('is_mandiri, count(*) as total')
            ->groupBy('is_mandiri')
            ->get();

        // 3. Data Doughnut: Temuan Sendiri vs Laporan
        $sourceData = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->selectRaw('temuan_sendiri, count(*) as total')
            ->groupBy('temuan_sendiri')
            ->get();

        // 4. Data Line Chart (7 Hari Terakhir)
        $trendData = DB::table('daily_reports')
            ->leftJoin('kegiatan_detail', 'daily_reports.id', '=', 'kegiatan_detail.daily_report_id')
            ->where('daily_reports.user_id', $user->id)
            ->where('daily_reports.tanggal', '>=', Carbon::now()->subDays(6)->toDateString())
            ->selectRaw('daily_reports.tanggal, count(kegiatan_detail.id) as total')
            ->groupBy('daily_reports.tanggal')
            ->orderBy('daily_reports.tanggal', 'ASC')
            ->get();

        return view('staff.dashboard', compact(
            'dailyCount',
            'weeklyCount',
            'monthlyCount',
            'autonomyData',
            'sourceData',
            'trendData'
        ));
    }
}
