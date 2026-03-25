<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class KpiAuditSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function title(): string
    {
        return 'Audit Penilaian KPI';
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

        $auditData = [];

        foreach ($users as $user) {
            // Kita butuh relasi shift untuk menghitung jam telat
            $reports = DailyReport::with('shift')->where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'approved')->get();

            $reportIds = $reports->pluck('id');
            $details = KegiatanDetail::whereIn('daily_report_id', $reportIds)->get();

            $failList = [];

            // 1. Cek Laporan Telat (Ontime) & Hitung Jam Selisih
            foreach ($reports->where('is_dashboard_ontime', 0) as $r) {
                $submitTime = $r->created_at;

                // Ambil jam pulang dari shift, jika tidak ada asumsikan jam 17:00
                $jamPulangStr = $r->shift ? $r->shift->jam_pulang : '17:00:00';

                // Buat objek Carbon deadline pada tanggal laporan
                $deadline = Carbon::parse($r->created_at->format('Y-m-d') . ' ' . $jamPulangStr);

                // PERBAIKAN: Gunakan (int) atau floor untuk membulatkan ke bawah
                $diffInHours = (int) $deadline->diffInHours($submitTime);
                $diffInMinutes = $deadline->diffInMinutes($submitTime) % 60;

                $failList[] = [
                    'tanggal' => $r->tanggal->format('d/m/Y'),
                    'aspek' => 'Report Ontime',
                    'keterangan' => 'Terlambat ' . $diffInHours . ' Jam ' . $diffInMinutes . ' Menit (Batas: ' . $deadline->format('H:i') . ', Submit: ' . $submitTime->format('H:i d/m') . ')',
                    'impact' => '- Poin Disiplin'
                ];
            }

            // 2. Cek Kasus TAC (Respon, Mandiri, & Temuan)
            if ($user->divisi_id == 1) {
                $networkCases = $details->where('kategori', 'Network')->filter(function ($d) {
                    return !str_contains(strtolower($d->deskripsi_kegiatan), 'monitoring');
                });

                foreach ($networkCases as $c) {
                    // A. Cek Respon Lambat
                    if ($c->waktu_respon_menit > 15 && $c->temuan_sendiri == 0) {
                        $failList[] = [
                            'tanggal' => $c->dailyReport->tanggal->format('d/m/Y'),
                            'aspek' => 'Respon Time',
                            'keterangan' => 'Respon lambat: ' . $c->waktu_respon_menit . ' menit (Max 15m). Deskripsi: ' . $c->deskripsi_kegiatan,
                            'impact' => '- Poin Kecepatan'
                        ];
                    }

                    // B. Cek Eskalasi (Bukan Mandiri)
                    if ($c->is_mandiri == 0) {
                        $failList[] = [
                            'tanggal' => $c->dailyReport->tanggal->format('d/m/Y'),
                            'aspek' => 'Mandiri',
                            'keterangan' => 'Kasus dieskalasi ke: ' . ($c->pic_name ?? 'Tim Lain'),
                            'impact' => '- Poin Skill Mandiri'
                        ];
                    }

                    // C. Cek Bukan Temuan Sendiri (Poin Proaktif)
                    if ($c->temuan_sendiri == 0) {
                        $failList[] = [
                            'tanggal' => $c->dailyReport->tanggal->format('d/m/Y'),
                            'aspek' => 'Proaktif (Temuan)',
                            'keterangan' => 'Kasus berdasarkan laporan (Bukan temuan mandiri staf)',
                            'impact' => '- Poin Inisiatif'
                        ];
                    }
                }
            }

            if (count($failList) > 0) {
                $auditData[] = ['user' => $user, 'fails' => $failList];
            }
        }

        return view('manager.exports.sheet_kpi_audit', ['auditData' => $auditData]);
    }
}
