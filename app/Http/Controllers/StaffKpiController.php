<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        // 1. Validasi minimal harus ada satu input, baik itu case atau activity
        if (!$request->has('case') && !$request->has('activity')) {
            return back()->with('error', 'Mohon isi setidaknya satu kegiatan (Case atau Activity).');
        }

        // 2. Buat satu header DailyReport untuk semua kegiatan yang di-submit saat ini
        $report = DailyReport::create([
            'user_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'status'  => 'pending',
        ]);

        $totalPoinHarian = 0;

        // Ambil variabel KPI untuk perhitungan poin (Hanya berlaku untuk tipe 'case')
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();
        $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();
        $vRespons = $variabelKpis->where('nama_variabel', 'Durasi Response (Ambang Batas 15 Menit)')->first();
        $vTemuan = $variabelKpis->where('nama_variabel', 'Case Ditemukan Sendiri')->first();
        $vMandiri = $variabelKpis->where('nama_variabel', 'Penyelesaian Mandiri (Bonus)')->first();

        // 3. PROSES INPUT TECHNICAL CASE (Jika Ada)
        if ($request->has('case')) {
            foreach ($request->case as $item) {
                $poinCaseIni = 0;

                // Hitung Poin KPI
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

                // Simpan ke kegiatan_detail dengan tipe 'case'
                KegiatanDetail::create([
                    'daily_report_id'    => $report->id,
                    'tipe_kegiatan'      => 'case', // Set manual ke 'case'
                    'variabel_kpi_id'    => $vCount ? $vCount->id : null, // Mengacu pada KPI dasar
                    'deskripsi_kegiatan' => $item['deskripsi'],
                    'value_raw'          => $item['respons'] ?? 0,
                    'temuan_sendiri'     => $isTemuan ? 1 : 0,
                    'is_mandiri'         => $item['is_mandiri'] ?? 1,
                    'pic_name'           => ($item['is_mandiri'] ?? '1') == '0' ? ($item['pic_name'] ?? '') : null,
                ]);

            }
        }

        // 4. PROSES INPUT GENERAL ACTIVITY (Jika Ada)
        if ($request->has('activity')) {
            foreach ($request->activity as $act) {
                // Simpan ke kegiatan_detail dengan tipe 'activity'
                KegiatanDetail::create([
                    'daily_report_id'    => $report->id,
                    'tipe_kegiatan'      => 'activity', // Set manual ke 'activity'
                    'variabel_kpi_id'    => null, // Activity umum tidak masuk hitungan KPI
                    'deskripsi_kegiatan' => $act['deskripsi'],
                    'value_raw'          => null, // Tidak ada durasi respons untuk activity
                    'temuan_sendiri'     => 0,
                    'is_mandiri'         => 1,
                    'pic_name'           => null,
                ]);
            }
        }

        // 5. Update total nilai pada header report

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

    public function logs(Request $request)
    {
        $query = DailyReport::with('details')
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
        // Cari detail case, pastikan milik user yang login melalui daily_report
        $detail = KegiatanDetail::where('id', $id)
            ->whereHas('dailyReport', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'pending');
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

    // =========================================================
    // Modul Achievements Stats
    // ========================================================

    public function achievements()
    {
        $userId = Auth::id();
        $year = now()->year;

        $reports = DailyReport::where('user_id', $userId)
            ->whereYear('tanggal', $year)
            ->withCount('details')
            ->get()
            ->keyBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $heatmapData = [];

        for ($i = 0; $i < 7; $i++) {
            $dataPerDay = [];

            for ($w = 0; $w <= 53; $w++) {
                $date = now()->year($year)->startOfYear()->startOfWeek(0)->addWeeks($w)->addDays($i);

                $dateString = $date->format('Y-m-d');
                $count = isset($reports[$dateString]) ? $reports[$dateString]->details_count : 0;

                $dataPerDay[] = [
                    'x' => 'W' . ($w + 1),
                    'y' => $count,
                    'date' => $date->format('d M Y')
                ];
            }

            $heatmapData[] = [
                'name' => $days[$i],
                'data' => $dataPerDay
            ];
        }

        $heatmapData = array_reverse($heatmapData);

        // 2. Data untuk Radar Chart
        $userStats = [
            'Kemandirian' => $this->getAutonomyRate($userId),
            'Inisiatif'   => $this->getProactiveRate($userId),
            'Volume' => DailyReport::where('user_id', $userId)->withCount('details')->get()->avg('details_count') * 10,
            'Respons'     => $this->getUserResponseScore($userId),
        ];

        // 3. Data Rata-rata Tim
        $teamAverage = [
            'Kemandirian' => $this->getGlobalAutonomyAverage(),
            'Inisiatif'   => $this->getGlobalProactiveAverage(),
            'Volume'      => $this->getGlobalVolumeAverage(),
            'Respons'     => $this->getGlobalResponseAverage(),
        ];

        return view('staff.achievements', compact('heatmapData', 'userStats', 'teamAverage'));
    }

    /**
     * Rumus konversi menit ke skor 0-100
     */
    private function calculateScoreFromMinutes($minutes)
    {
        if ($minutes <= 0) return 0;

        // Batas minimum time respons
        $goldStandard = 15;

        if ($minutes <= $goldStandard) {
            return 100;
        }

        /** * Jika di atas 15 menit, kita kurangi skornya secara linear.
         * Rumus: 100 - (kelebihan menit). 
         * Contoh: 25 menit -> 100 - (25 - 15) = 90.
         * Contoh: 60 menit -> 100 - (60 - 15) = 55.
         */
        $score = 100 - ($minutes - $goldStandard);

        return max($score, 10);
    }

    // Helper sederhana untuk hitung persentase
    private function getAutonomyRate($userId)
    {
        $total = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $userId))->count();
        $mandiri = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $userId))->where('is_mandiri', 1)->count();
        return $total > 0 ? ($mandiri / $total) * 100 : 0;
    }

    private function getProactiveRate($userId)
    {
        $total = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $userId))->count();
        $temuan = KegiatanDetail::whereHas('dailyReport', fn($q) => $q->where('user_id', $userId))->where('temuan_sendiri', 1)->count();
        return $total > 0 ? ($temuan / $total) * 100 : 0;
    }

    private function getUserResponseScore($userId)
    {
        // Ambil rata-rata menit (value_raw) milik user
        $avgMinutes = KegiatanDetail::whereHas('dailyReport', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->avg('value_raw') ?? 0;

        return $this->calculateScoreFromMinutes($avgMinutes);
    }

    // Menghitung berapa persen rata-rata staff bekerja mandiri
    private function getGlobalAutonomyAverage()
    {
        $total = KegiatanDetail::count();
        $mandiri = KegiatanDetail::where('is_mandiri', 1)->count();
        return $total > 0 ? ($mandiri / $total) * 100 : 0;
    }

    // Menghitung berapa persen rata-rata staff menemukan case sendiri (proaktif)
    private function getGlobalProactiveAverage()
    {
        $total = KegiatanDetail::count();
        $temuan = KegiatanDetail::where('temuan_sendiri', 1)->count();
        return $total > 0 ? ($temuan / $total) * 100 : 0;
    }

    // Menghitung rata-rata jumlah case per laporan di seluruh tim (normalisasi ke 100)
    private function getGlobalVolumeAverage()
    {
        $avg = DailyReport::withCount('details')->get()->avg('details_count');
        return min(($avg / 10) * 100, 100); // Kita asumsikan 10 case/hari adalah 100%
    }
    private function getGlobalResponseAverage()
    {
        // Ambil rata-rata menit (value_raw) dari SELURUH tim
        $avgMinutes = KegiatanDetail::avg('value_raw') ?? 0;
        return $this->calculateScoreFromMinutes($avgMinutes);
    }

    // =====================================================================
    // Modul Update Profile
    // =====================================================================
    public function editProfile()
    {
        $user = Auth::user();
        return view('staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email_prefix' => 'required|string|max:100',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update data dasar
        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;

        // Gabungkan prefix dengan domain tetap
        $user->email = $request->email_prefix . '@mybolo.com';

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
