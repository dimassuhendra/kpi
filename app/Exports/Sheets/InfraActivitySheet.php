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
        $query = KegiatanDetail::with(['dailyReport.user.divisi']);
        $lemburQuery = LemburReport::with(['dailyReport.user.divisi']);

        // CEK: Jika user_id KOSONG (berarti ini export per divisi), ambil SEMUA user infra
        if (empty($this->filters['user_id'])) {
            $query->whereHas('dailyReport.user', function ($q) {
                $q->where('divisi_id', 2);
            });
            $lemburQuery->whereHas('dailyReport.user', function ($q) {
                $q->where('divisi_id', 2);
            });
            $namaLengkap = 'Seluruh Anggota Divisi Infrastruktur';
        }
        // JIKA ADA user_id (berarti ini export per user biasa)
        else {
            $query->whereHas('dailyReport', function ($q) {
                $q->where('user_id', $this->filters['user_id']);
            });
            $lemburQuery->whereHas('dailyReport', function ($q) {
                $q->where('user_id', $this->filters['user_id']);
            });
            $user = User::find($this->filters['user_id']);
            $namaLengkap = $user ? $user->nama_lengkap : 'User Infra';
        }

        // Filter Tanggal
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $dateRange = [$this->filters['start_date'], $this->filters['end_date']];
            $query->whereHas('dailyReport', function ($q) use ($dateRange) {
                $q->whereBetween('tanggal', $dateRange);
            });
            $lemburQuery->whereHas('dailyReport', function ($q) use ($dateRange) {
                $q->whereBetween('tanggal', $dateRange);
            });
        }

        $kegiatans = $query->get();
        $lemburs = $lemburQuery->get();

        return view('manager.exports.infra_activity', [
            'kegiatans' => $kegiatans,
            'lemburs'   => $lemburs,
            'nama'      => $namaLengkap,
            'divisi'    => 'Infrastruktur',
            'periode'   => ($this->filters['start_date'] ?? '-') . ' s/d ' . ($this->filters['end_date'] ?? '-')
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
            'C' => 25,  // Nama Staff (Kolom Baru)
            'D' => 20,  // Kategori
            'E' => 35,  // Judul Kegiatan
            'F' => 55,  // Deskripsi Kegiatan
            'G' => 35,  // Bukti Dokumentasi
        ];
    }

    // ==========================================
    // 2. Atur Style Wrap Text & Alignment Di Sini
    // ==========================================
    public function styles(Worksheet $sheet)
    {
        // Teks rata atas untuk semua cell A sampai G
        $sheet->getStyle('A:G')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Wrap text untuk kolom teks panjang (sekarang bergeser ke E, F, G)
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);

        return [];
    }
}
