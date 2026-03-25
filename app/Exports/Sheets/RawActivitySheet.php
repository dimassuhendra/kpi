<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\DailyReport;
use App\Models\KegiatanDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RawActivitySheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function title(): string
    {
        return 'Log Aktivitas Harian';
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

        $dataExport = [];

        foreach ($users as $user) {
            $reports = DailyReport::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'approved')->orderBy('tanggal', 'asc')->get();

            $reportIds = $reports->pluck('id');
            // AMBIL SEMUA TANPA TERKECUALI (Termasuk Monitoring & GPS)
            $details = KegiatanDetail::whereIn('daily_report_id', $reportIds)->get();

            $dataExport[] = ['user' => $user, 'reports' => $reports, 'details' => $details];
        }

        return view('manager.exports.sheet_raw_activity', ['dataExport' => $dataExport]);
    }
}
