<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KpiDivisiExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        // Kita menggunakan ulang Sheet yang sama agar rapi,
        // karena logic filter divisinya akan kita tambahkan di dalam masing-masing sheet.
        return [
            new Sheets\KpiSummarySheet($this->filters),
            new Sheets\RawActivitySheet($this->filters),
            new Sheets\RatingSheet($this->filters),
            new Sheets\QuizSheet($this->filters),
            new Sheets\KpiAuditSheet($this->filters),
        ];
    }
}
