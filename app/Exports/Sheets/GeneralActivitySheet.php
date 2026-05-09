<?php

namespace App\Exports\Sheets;

use App\Models\KegiatanDetail;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralActivitySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $rowNumber = 0;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $startDate = $this->filters['start_date'] ?? date('Y-m-01');
        $endDate = $this->filters['end_date'] ?? date('Y-m-d');

        // Cek user_id untuk export individu dari Manager atau Staff ybs
        $userId = is_array($this->filters)
            ? ($this->filters['user_id'] ?? null)
            : ($this->filters->user_id ?? null);

        $query = KegiatanDetail::with('dailyReport.user.divisi')
            ->whereHas('dailyReport', function ($q) use ($startDate, $endDate, $userId) {
                $q->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('status', 'approved');

                if ($userId) {
                    $q->where('user_id', $userId);
                }
            });

        // Urutkan berdasarkan tanggal laporan (terlama ke terbaru)
        return $query->get()->sortBy(function ($item) {
            return $item->dailyReport->tanggal;
        });
    }

    public function headings(): array
    {
        // 1. Dapatkan Nama Staff dan Divisi langsung dari relasi DATABASE
        $userId = is_array($this->filters) ? ($this->filters['user_id'] ?? null) : ($this->filters->user_id ?? null);
        $namaStaff = 'Semua Staff';
        $namaDivisiDariUser = null;

        if ($userId) {
            $user = User::with('divisi')->find($userId);
            if ($user) {
                $namaStaff = $user->nama_lengkap;
                // Menarik langsung dari field 'nama_divisi' di database
                $namaDivisiDariUser = $user->divisi->nama_divisi ?? null;
            }
        }

        // 2. Dapatkan Nama Divisi (Mendukung param array 'nama_divisi' atau 'divisi_name' dari controller)
        $divisiParam = null;
        if (is_array($this->filters)) {
            $divisiParam = $this->filters['nama_divisi'] ?? $this->filters['divisi_name'] ?? null;
        } elseif (is_object($this->filters)) {
            $divisiParam = $this->filters->nama_divisi ?? $this->filters->divisi_name ?? null;
        }

        // Prioritas pencarian: Param Array Controller -> Relasi Database -> 'UMUM'
        $divisi = strtoupper($divisiParam ?? $namaDivisiDariUser ?? 'UMUM');

        // 3. Dapatkan Periode Download dengan format tanggal Indonesia yang rapi
        $start = isset($this->filters['start_date'])
            ? Carbon::parse($this->filters['start_date'])->translatedFormat('d F Y')
            : Carbon::now()->startOfMonth()->translatedFormat('d F Y');

        $end = isset($this->filters['end_date'])
            ? Carbon::parse($this->filters['end_date'])->translatedFormat('d F Y')
            : Carbon::now()->translatedFormat('d F Y');

        $periode = $start . ' s/d ' . $end;

        // Return Multi-dimensi Array untuk membuat Kop Surat / Header Laporan
        return [
            ['LAPORAN KEGIATAN HARIAN'],
            ['Divisi', ': ' . $divisi],
            ['Nama Staff', ': ' . $namaStaff],
            ['Periode', ': ' . $periode],
            [''], // Baris ke-5 dikosongkan sebagai spasi
            ['NO', 'TANGGAL', 'JUDUL KEGIATAN', 'DESKRIPSI'] // Baris ke-6 adalah header tabel data
        ];
    }

    public function map($detail): array
    {
        $this->rowNumber++;

        // Ekstraktor otomatis untuk memisahkan Judul dan Deskripsi
        $judul = $detail->nama_kegiatan ?? '';
        $deskripsi = $detail->deskripsi_kegiatan ?? '';

        if (empty($judul) && !empty($deskripsi)) {
            if (strpos($deskripsi, ': ') !== false) {
                $parts = explode(': ', $deskripsi, 2);
                $judul = $parts[0];
                $deskripsi = trim($parts[1]);
            } else {
                $judul = $deskripsi;
                $deskripsi = '';
            }
        }

        if (trim($deskripsi) === '-') {
            $deskripsi = '';
        }

        return [
            $this->rowNumber,
            $detail->dailyReport->tanggal->format('d/m/Y'),
            $judul,
            $deskripsi
        ];
    }

    public function title(): string
    {
        return 'Laporan Kegiatan';
    }

    public function styles(Worksheet $sheet)
    {
        // Gabungkan sel (Merge) untuk Judul Utama agar rata tengah di atas tabel
        $sheet->mergeCells('A1:D1');

        return [
            // Style untuk Judul Utama Laporan (Baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Style untuk label Kop Laporan (Baris 2, 3, 4 pada Kolom A)
            'A2:A4' => [
                'font' => ['bold' => true],
            ],

            // Style untuk Header Tabel Data (Kini berada di Baris ke-6)
            6 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'] // Warna abu-abu elegan
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

            // Kolom Deskripsi (D) diberi wrap text agar teks yang panjang tidak bablas menyamping
            'D' => [
                'alignment' => ['wrapText' => true],
            ],
        ];
    }
}
