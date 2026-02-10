<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanDetail extends Model
{
    protected $table = 'kegiatan_detail';
    protected $fillable = [
        'daily_report_id',
        'variabel_kpi_id',
        'tipe_kegiatan',
        'deskripsi_kegiatan',
        'value_raw',
        'temuan_sendiri',
        'is_mandiri',
        'pic_name',
        'nilai_akhir'
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function variabelKpi()
    {
        return $this->belongsTo(VariabelKpi::class, 'variabel_kpi_id');
    }
}
