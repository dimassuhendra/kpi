<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\User;

class KpiExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $divisiId = 1; // Default ke 1 (TAC)

        // 1. Cek jika $filters adalah array dan punya divisi_id
        if (is_array($this->filters) && isset($this->filters['divisi_id'])) {
            $divisiId = $this->filters['divisi_id'];
        }
        // 2. Cek jika $filters adalah object dan punya divisi_id
        elseif (is_object($this->filters) && isset($this->filters->divisi_id)) {
            $divisiId = $this->filters->divisi_id;
        }
        // 3. BACKUP PLAN: Jika divisi_id tidak dikirim, tapi user_id dikirim
        elseif (isset($this->filters['user_id']) || (is_object($this->filters) && isset($this->filters->user_id))) {
            $userId = is_array($this->filters) ? $this->filters['user_id'] : $this->filters->user_id;

            $user = User::find($userId);
            if ($user && $user->divisi_id) {
                $divisiId = $user->divisi_id;
            }
        }

        // ==============================================================
        // LOGIKA PEMISAHAN SHEET BERDASARKAN DIVISI
        // ==============================================================

        // Jika divisi == 2 (Infra)
        if ($divisiId == 2) {
            return [
                new Sheets\InfraActivitySheet($this->filters),    // Sheet Aktivitas Infra
            ];
        }
        // Jika divisi BUKAN 1 (TAC) dan BUKAN 2 (Infra) -> Berlaku untuk BOT, Purchasing, dsb.
        else if ($divisiId == 4 || $divisiId == 5) {
            return [
                new Sheets\GeneralActivitySheet($this->filters),  // Sheet Simple BOT/Purchasing
            ];
        }

        // Default: Jika divisi == 1 (TAC)
        return [
            new Sheets\KpiSummarySheet($this->filters),  // Sheet 1: Rekap Nilai KPI
            new Sheets\RawActivitySheet($this->filters), // Sheet 2: Semua Aktivitas Mentah
            new Sheets\RatingSheet($this->filters),      // Sheet 3: Rating Pelanggan
            new Sheets\QuizSheet($this->filters),        // Sheet 4: Hasil Kuis
            new Sheets\KpiAuditSheet($this->filters),    // Sheet 5: Audit Bukti Potong Poin
        ];
    }
}
