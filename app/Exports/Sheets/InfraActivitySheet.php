<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\View\View;
use App\Models\KegiatanDetail;
use App\Models\LemburReport; // <-- Tambahkan model Lembur
use App\Models\User; // <-- Tambahkan model User

class InfraActivitySheet implements FromView, WithTitle, WithColumnWidths, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        // 1. Query Kegiatan Reguler
        $query = KegiatanDetail::with(['dailyReport.user.divisi']);
        // 2. Query Kegiatan Lembur
        $lemburQuery = LemburReport::with(['dailyReport.user.divisi']);

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereHas('dailyReport', function ($q) {
                $q->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
            });
            $lemburQuery->whereHas('dailyReport', function ($q) {
                $q->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
            });
        }

        if (!empty($this->filters['user_id'])) {
            $query->whereHas('dailyReport', function ($q) {
                $q->where('user_id', $this->filters['user_id']);
            });
            $lemburQuery->whereHas('dailyReport', function ($q) {
                $q->where('user_id', $this->filters['user_id']);
            });
        }

        $kegiatans = $query->get();
        $lemburs = $lemburQuery->get();

        // 3. Menentukan Nama User
        $namaLengkap = 'Semua User Infra';
        if (!empty($this->filters['user_id'])) {
            $user = User::find($this->filters['user_id']);
            if ($user) $namaLengkap = $user->nama_lengkap;
        } elseif ($kegiatans->isNotEmpty()) {
            $namaLengkap = $kegiatans->first()->dailyReport->user->nama_lengkap;
        } elseif ($lemburs->isNotEmpty()) {
            $namaLengkap = $lemburs->first()->dailyReport->user->nama_lengkap;
        }

        $divisi = 'Infrastruktur';
        $periodeStart = $this->filters['start_date'] ?? '-';
        $periodeEnd = $this->filters['end_date'] ?? '-';
        $periode = $periodeStart . ' s/d ' . $periodeEnd;

        return view('manager.exports.infra_activity', [
            'kegiatans' => $kegiatans,
            'lemburs'   => $lemburs, // <-- Lempar data lembur ke Blade
            'nama'      => $namaLengkap,
            'divisi'    => $divisi,
            'periode'   => $periode
        ]);
    }

    public function title(): string
    {
        return 'Aktivitas Infra';
    }

    // ==========================================
    // 1. Atur Lebar Kolom Di Sini
    // ==========================================
    public function columnWidths(): array
    {
        // Diperbaiki urutannya agar sesuai dengan 6 Kolom di Blade (A sampai F)
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 20,  // Kategori
            'D' => 35,  // Judul Kegiatan
            'E' => 55,  // Deskripsi Kegiatan (Paling Lebar)
            'F' => 35,  // Bukti Dokumentasi
        ];
    }

    // ==========================================
    // 2. Atur Style Wrap Text & Alignment Di Sini
    // ==========================================
    public function styles(Worksheet $sheet)
    {
        // Teks rata atas untuk semua cell A sampai F
        $sheet->getStyle('A:F')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Wrap text untuk judul, deskripsi, dan dokumentasi
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);

        return [];
    }
}
