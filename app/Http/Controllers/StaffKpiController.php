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
use App\Models\Divisi;
use App\Models\KegiatanDetail;

class StaffKpiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil variabel KPI sesuai divisi (untuk TAC biasanya Case, untuk Infra biasanya Kategori)
        $variabelKpis = VariabelKpi::where('divisi_id', $user->divisi_id)->get();

        // Logika baris awal (formattedRows)
        if ($user->divisi_id == 2) {
            // Baris awal default untuk tim Infrastruktur
            $formattedRows = [[
                'nama_kegiatan' => '',
                'deskripsi' => '',
                'kategori' => 'Network',
            ]];
        } else {
            // Baris awal default untuk tim TAC
            $formattedRows = [[
                'deskripsi' => '',
                'respons' => '',
                'temuan_sendiri' => false,
                'is_mandiri' => 1,
                'pic_name' => ''
            ]];
        }

        // SEMUA DIVISI diarahkan ke file yang sama: staff/input_kpi.blade.php
        return view('staff.input_kpi', compact('variabelKpis', 'formattedRows'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi awal
        if ($user->divisi_id == 1) { // Logika TAC
            if (!$request->has('case') && !$request->has('activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan.');
            }
        } else { // Logika Infra
            if (!$request->has('infra_activity')) {
                return back()->with('error', 'Mohon isi setidaknya satu kegiatan infrastruktur.');
            }
        }

        // 2. Buat Header DailyReport
        $report = DailyReport::create([
            'user_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'status'  => 'pending',
        ]);

        // ---------------------------------------------------------
        // LOGIKA PROSES: DIVISI TAC (DIVISI ID: 1)
        // ---------------------------------------------------------
        if ($user->divisi_id == 1) {
            $variabelKpis = VariabelKpi::where('divisi_id', 1)->get();
            $vCount = $variabelKpis->where('nama_variabel', 'Jumlah Case Harian')->first();
            $vRespons = $variabelKpis->where('nama_variabel', 'Durasi Response (Ambang Batas 15 Menit)')->first();
            $vTemuan = $variabelKpis->where('nama_variabel', 'Case Ditemukan Sendiri')->first();
            $vMandiri = $variabelKpis->where('nama_variabel', 'Penyelesaian Mandiri (Bonus)')->first();

            // Proses Case TAC
            if ($request->has('case')) {
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
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'case',
                        'variabel_kpi_id'    => $vCount ? $vCount->id : null,
                        'deskripsi_kegiatan' => $item['deskripsi'],
                        'value_raw'          => $item['respons'] ?? 0,
                        'temuan_sendiri'     => $isTemuan ? 1 : 0,
                        'is_mandiri'         => $item['is_mandiri'] ?? 1,
                        'pic_name'           => ($item['is_mandiri'] ?? '1') == '0' ? ($item['pic_name'] ?? '') : null,
                    ]);
                }
            }

            // Proses General Activity TAC
            if ($request->has('activity')) {
                foreach ($request->activity as $act) {
                    KegiatanDetail::create([
                        'daily_report_id'    => $report->id,
                        'tipe_kegiatan'      => 'activity',
                        'deskripsi_kegiatan' => $act['deskripsi'],
                    ]);
                }
            }
        }

        // ---------------------------------------------------------
        // LOGIKA PROSES: DIVISI INFRASTRUKTUR (DIVISI ID: 2)
        // ---------------------------------------------------------
        // DI DALAM FUNCTION store()
        else if ($user->divisi_id == 2) {
            $vInfra = VariabelKpi::where('divisi_id', 2)->where('nama_variabel', 'Volume Pekerjaan')->first();

            foreach ($request->infra_activity as $infra) {
                KegiatanDetail::create([
                    'daily_report_id'    => $report->id,
                    'tipe_kegiatan'      => 'activity',

                    // 1. INI YANG PALING PENTING: Simpan kategorinya ke kolom kategori
                    'kategori'           => $infra['kategori'],

                    // 2. Deskripsi biar rapi, tidak perlu pakai kurung siku lagi kalau sudah ada kolomnya
                    'deskripsi_kegiatan' => $infra['nama_kegiatan'] . ': ' . $infra['deskripsi'],

                    'variabel_kpi_id'    => $vInfra ? $vInfra->id : null,
                ]);
            }
        }

        return redirect()->route('staff.input')->with('success', 'Laporan Berhasil Disimpan!');
    }

    public function dashboard()
    {
        $user = Auth::user(); // Tidak perlu load divisi kalau cuma cek ID
        $now = Carbon::now();

        // --- DATA UMUM ---
        $dailyCount = DB::table('kegiatan_detail')
            ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->whereDate('daily_reports.tanggal', $now->toDateString())
            ->count();

        $trendData = DB::table('daily_reports')
            ->leftJoin('kegiatan_detail', 'daily_reports.id', '=', 'kegiatan_detail.daily_report_id')
            ->where('daily_reports.user_id', $user->id)
            ->where('daily_reports.tanggal', '>=', $now->copy()->subDays(6)->toDateString())
            ->selectRaw('daily_reports.tanggal, count(kegiatan_detail.id) as total')
            ->groupBy('daily_reports.tanggal')
            ->orderBy('daily_reports.tanggal', 'ASC')
            ->get();

        // Inisialisasi variabel agar tidak error di blade
        $infraWorkload = [];
        $staffInfraData = [];
        $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];
        $autonomyData = collect();
        $sourceData = collect();
        $weeklyCount = 0;
        $monthlyCount = 0;

        // --- LOGIKA DATA BERDASARKAN ID DIVISI ---
        if ($user->divisi_id == 2) {
            // Tentukan bulan dan tahun saat ini
            $currentMonth = $now->month;
            $currentYear = $now->year;

            $availableCategories = ['Network', 'CCTV', 'GPS', 'Lainnya'];

            // 1. Workload Distribution (HANYA BULAN INI)
            $infraWorkload = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $currentMonth) // Filter Bulan
                ->whereYear('daily_reports.tanggal', $currentYear)   // Filter Tahun
                ->selectRaw('kategori, count(*) as total')
                ->groupBy('kategori')
                ->pluck('total', 'kategori')->toArray();

            // 2. Staff Technical Focus (HANYA BULAN INI)
            $staffInfraData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->whereMonth('daily_reports.tanggal', $currentMonth) // Filter Bulan
                ->whereYear('daily_reports.tanggal', $currentYear)   // Filter Tahun
                ->selectRaw('daily_reports.tanggal as tgl, kategori, count(*) as total')
                ->groupBy('daily_reports.tanggal', 'kategori')
                ->orderBy('daily_reports.tanggal', 'ASC') // Urutkan dari tanggal awal bulan
                ->get()
                ->groupBy('tgl')
                ->map(function ($items, $date) use ($availableCategories) {
                    $res = ['nama' => date('d M', strtotime($date))];
                    foreach ($availableCategories as $cat) {
                        $res[$cat] = $items->where('kategori', $cat)->first()->total ?? 0;
                    }
                    return $res;
                })->values();
        } else {
            // DIVISI TAC (Default)
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

            $autonomyData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->selectRaw('is_mandiri, count(*) as total')
                ->groupBy('is_mandiri')->get();

            $sourceData = DB::table('kegiatan_detail')
                ->join('daily_reports', 'kegiatan_detail.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.user_id', $user->id)
                ->selectRaw('temuan_sendiri, count(*) as total')
                ->groupBy('temuan_sendiri')->get();
        }

        // Kirim ke SATU file blade yang sama
        return view('staff.dashboard', compact(
            'dailyCount',
            'trendData',
            'infraWorkload',
            'staffInfraData',
            'availableCategories',
            'weeklyCount',
            'monthlyCount',
            'autonomyData',
            'sourceData'
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
