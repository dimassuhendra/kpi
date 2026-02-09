<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KpiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DailyReport::with('user')->where('status', 'approved');

        if ($this->filters['user_id']) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if ($this->filters['start_date'] && $this->filters['end_date']) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Staff',
            'Total Nilai',
            'Catatan Manager',
            'Status'
        ];
    }

    public function map($report): array
    {
        return [
            $report->tanggal,
            $report->user->nama_lengkap,
            number_format($report->total_nilai_harian, 1),
            $report->catatan_manager,
            $report->status
        ];
    }
}
