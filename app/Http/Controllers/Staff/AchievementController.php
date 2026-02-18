<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\DailyReport;
use App\Models\KegiatanDetail;

class AchievementController extends Controller
{
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
}
