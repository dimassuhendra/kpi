<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LemburReport extends Model
{
    use HasFactory;

    protected $table = 'lembur_reports';

    protected $fillable = [
        'daily_report_id',
        'waktu_mulai',
        'waktu_selesai',
        'detail_pekerjaan',
        'foto_dokumentasi',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
