<?php

namespace App\Exports;

use App\Models\KegiatanDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = KegiatanDetail::with(['dailyReport.user.divisi']);

        $query->whereHas('dailyReport', function ($q) {
            $q->where('status', 'approved');

            if (!empty($this->filters['user_id'])) {
                $q->where('user_id', $this->filters['user_id']);
            }

            if (!empty($this->filters['start_date'])) {
                $q->whereDate('tanggal', '>=', $this->filters['start_date']);
            }

            if (!empty($this->filters['end_date'])) {
                $q->whereDate('tanggal', '<=', $this->filters['end_date']);
            }
        });

        return $query;
    }

    public function headings(): array
    {
        // Header dibuat mencakup kedua kebutuhan, namun mappingnya nanti yang membedakan
        return [
            'ID LAPORAN',
            'TANGGAL',
            'NAMA STAFF',
            'DIVISI',
            'TIPE KEGIATAN (TAC)',
            'KATEGORI (INFRA)',
            'JUDUL CASE/KEGIATAN',
            'DESKRIPSI (INFRA)',
            'PROAKTIF/TEMUAN SENDIRI (TAC)',
            'STATUS MANDIRI (TAC)',
            'DURASI (TAC - MENIT)',
        ];
    }

    public function map($kegiatan): array
    {
        $divisiId = $kegiatan->dailyReport->user->divisi_id;

        // Logika Kondisional: Jika Divisi TAC (ID: 1)
        if ($divisiId == 1) {
            return [
                $kegiatan->daily_report_id,
                $kegiatan->dailyReport->tanggal->format('d/m/Y'),
                $kegiatan->dailyReport->user->nama_lengkap,
                'TAC',
                strtoupper($kegiatan->tipe_kegiatan),
                '-', // Kategori kosong untuk TAC
                $kegiatan->deskripsi_kegiatan,
                '-', // Deskripsi kosong untuk TAC (sesuai permintaan)
                $kegiatan->temuan_sendiri ? 'Ya (Inisiatif)' : 'Tidak (Laporan)',
                $kegiatan->is_mandiri ? 'Mandiri' : 'Bantuan',
                $kegiatan->value_raw,
            ];
        }

        // Logika Kondisional: Jika Divisi INFRA (ID: 2)
        else {
            return [
                $kegiatan->daily_report_id,
                $kegiatan->dailyReport->tanggal->format('d/m/Y'),
                $kegiatan->dailyReport->user->nama_lengkap,
                'INFRA',
                '-', // Tipe kegiatan kosong untuk Infra
                $kegiatan->kategori ?? 'Lainnya',
                $kegiatan->deskripsi_kegiatan,
                $kegiatan->deskripsi,
                '-', // Proaktif kosong untuk Infra
                '-', // Mandiri kosong untuk Infra
                '-', // Durasi kosong untuk Infra
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B']
                ],
            ],
        ];
    }
}
