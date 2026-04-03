<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // <-- Tambahkan ini
use Maatwebsite\Excel\Concerns\WithStyles;       // <-- Tambahkan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // <-- Tambahkan ini
use Illuminate\Contracts\View\View;
use App\Models\KegiatanDetail;

class InfraActivitySheet implements FromView, WithTitle, WithColumnWidths, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = KegiatanDetail::with(['dailyReport.user.divisi']);

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereHas('dailyReport', function ($q) {
                $q->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
            });
        }

        if (!empty($this->filters['user_id'])) {
            $query->whereHas('dailyReport', function ($q) {
                $q->where('user_id', $this->filters['user_id']);
            });
        }

        $kegiatans = $query->get();

        $namaLengkap = (!empty($this->filters['user_id']) && $kegiatans->isNotEmpty())
            ? $kegiatans->first()->dailyReport->user->nama_lengkap
            : 'Semua User Infra';

        $divisi = 'Infrastruktur';

        $periodeStart = $this->filters['start_date'] ?? '-';
        $periodeEnd = $this->filters['end_date'] ?? '-';
        $periode = $periodeStart . ' s/d ' . $periodeEnd;

        return view('manager.exports.infra_activity', [
            'kegiatans' => $kegiatans,
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
        return [
            'A' => 5,   // No
            'F' => 20,  // Tanggal
            'B' => 20,  // Kategori
            'C' => 35,  // Judul Kegiatan
            'D' => 55,  // Deskripsi Kegiatan (Paling Lebar)
            'E' => 45,  // Bukti Dokumentasi (URL biasanya panjang)
        ];
    }

    // ==========================================
    // 2. Atur Style Wrap Text & Alignment Di Sini
    // ==========================================
    public function styles(Worksheet $sheet)
    {
        // Mengatur agar semua teks sejajar di atas (Vertical Top) saat cell melebar ke bawah
        $sheet->getStyle('A:E')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Mengatur kolom D (Deskripsi) dan E (Bukti) agar otomatis Wrap Text
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);

        return [];
    }
}
