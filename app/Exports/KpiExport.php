<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KpiExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new Sheets\KpiSummarySheet($this->filters),   // Sheet 1: Rekap Nilai KPI
            new Sheets\RawActivitySheet($this->filters),  // Sheet 2: Semua Aktivitas Mentah
            new Sheets\RatingSheet($this->filters),       // Sheet 3: Rating Pelanggan
            new Sheets\QuizSheet($this->filters),         // Sheet 4: Hasil Kuis
            new Sheets\KpiAuditSheet($this->filters),    // <-- Sheet Baru: Audit Bukti Potong Poin
        ];
    }
}
