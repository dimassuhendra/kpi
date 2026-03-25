<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class KpiSummarySheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function title(): string
    {
        return 'Summary KPI';
    }

    public function view(): View
    {
        $startDate = $this->filters['start_date'];
        $endDate = $this->filters['end_date'];

        $usersQuery = User::with('divisi')->where('role', 'staff');
        if (!empty($this->filters['user_id'])) {
            $usersQuery->where('id', $this->filters['user_id']);
        }
        $users = $usersQuery->get();

        $dataKpi = [];

        foreach ($users as $user) {
            $reports = DailyReport::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'approved')->get();

            $reportIds = $reports->pluck('id');
            $details = KegiatanDetail::whereIn('daily_report_id', $reportIds)->get();

            $kpi = [];

            // HANYA HITUNG KPI JIKA DIA DIVISI TAC (ID = 1)
            if ($user->divisi_id == 1) {
                // 1. FILTER: Ambil Network, dan BUANG yang ada kata "monitoring"
                $validCases = $details->where('kategori', 'Network')->filter(function ($d) {
                    return !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring');
                });

                $totalCases = $validCases->count();
                $totalReports = $reports->count();

                // Ambil Data Kuis & Rating untuk user ini
                $quizzes = TechnicalAssessment::where('user_id', $user->id)
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->get();

                $feedbacks = CustomerFeedback::where('user_id', $user->id)
                    ->whereBetween('tanggal_survey', [$startDate, $endDate])
                    ->get();

                // --- START PERHITUNGAN ---

                // 1. Waktu Respon (Bobot 15%)
                $responCount = $validCases->filter(function ($c) {
                    return $c->waktu_respon_menit <= 15 || $c->temuan_sendiri == 1;
                })->count();
                $pureRespon = $totalCases > 0 ? ($responCount / $totalCases) * 100 : 0;
                $scoreRespon = ($pureRespon / 100) * 15;

                // 2. Mandiri (Bobot 15%)
                $mandiriCount = $validCases->where('is_mandiri', 1)->count();
                $pureMandiri = $totalCases > 0 ? ($mandiriCount / $totalCases) * 100 : 0;
                $scoreMandiri = ($pureMandiri / 100) * 15;

                // 3. Temuan Sendiri (Bobot 15%)
                $temuanCount = $validCases->where('temuan_sendiri', 1)->count();
                $pureTemuan = $totalCases > 0 ? ($temuanCount / $totalCases) * 100 : 0;
                $scoreTemuan = ($pureTemuan / 100) * 15;

                // 4. Report Ontime (Bobot 10%)
                $ontimeCount = $reports->where('is_dashboard_ontime', 1)->count();
                $pureOntime = $totalReports > 0 ? ($ontimeCount / $totalReports) * 100 : 0;
                $scoreOntime = ($pureOntime / 100) * 10;

                // 5. Quiz (Bobot 25%)
                $sumBenar = $quizzes->sum('jumlah_benar');
                $sumSoal = $quizzes->sum('jumlah_soal');
                $pureQuiz = $sumSoal > 0 ? ($sumBenar / $sumSoal) * 100 : 0;
                $scoreQuiz = ($pureQuiz / 100) * 25;

                // 6. Rating (Bobot 20%)
                $avgRating = $feedbacks->avg('rating') ?: 0;
                $pureRating = ($avgRating / 5) * 100;
                $scoreRating = ($pureRating / 100) * 20;

                $totalKpi = $scoreRespon + $scoreMandiri + $scoreTemuan + $scoreOntime + $scoreQuiz + $scoreRating;

                $kpi = [
                    'respon' => ['score' => round($scoreRespon, 2), 'pure' => round($pureRespon, 1), 'raw' => "$responCount/$totalCases Case"],
                    'mandiri' => ['score' => round($scoreMandiri, 2), 'pure' => round($pureMandiri, 1), 'raw' => "$mandiriCount/$totalCases Case"],
                    'temuan' => ['score' => round($scoreTemuan, 2), 'pure' => round($pureTemuan, 1), 'raw' => "$temuanCount/$totalCases Case"],
                    'ontime' => ['score' => round($scoreOntime, 2), 'pure' => round($pureOntime, 1), 'raw' => "$ontimeCount/$totalReports Hari"],
                    'quiz' => ['score' => round($scoreQuiz, 2), 'pure' => round($pureQuiz, 1), 'raw' => "$sumBenar/$sumSoal Soal"],
                    'rating' => ['score' => round($scoreRating, 2), 'pure' => round($pureRating, 1), 'raw' => round($avgRating, 1) . "/5"],
                    'total' => round($totalKpi, 2)
                ];
            }

            $dataKpi[] = ['user' => $user, 'kpi' => $kpi];
        }

        return view('manager.exports.sheet_kpi_summary', ['dataKpi' => $dataKpi, 'periode' => $startDate . ' s/d ' . $endDate]);
    }
}
