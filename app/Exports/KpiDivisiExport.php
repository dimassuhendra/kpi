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
        // Tangkap divisi_id yang dikirim Controller
        $divisiId = $this->filters['divisi_id'] ?? 1;

        // JIKA DIVISI INFRA (2) -> Panggil sheet gabungan Infra
        if ($divisiId == 2) {
            return [
                new Sheets\InfraActivitySheet($this->filters),
            ];
        }

        // DEFAULT: DIVISI TAC -> Panggil 5 Sheet TAC
        return [
            new Sheets\KpiSummarySheet($this->filters),
            new Sheets\RawActivitySheet($this->filters),
            new Sheets\RatingSheet($this->filters),
            new Sheets\QuizSheet($this->filters),
            new Sheets\KpiAuditSheet($this->filters),
        ];
    }
}
