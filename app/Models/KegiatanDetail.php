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
        'deskripsi_kegiatan',
        'value_raw',
        'temuan_sendiri',
        'is_mandiri',
        'pic_name',
        'nilai_akhir'
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function variabelKpi(): BelongsTo
    {
        return $this->belongsTo(VariabelKpi::class);
    }
}
