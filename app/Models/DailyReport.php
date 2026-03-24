<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'status',
        'shift_id',              
        'is_gps_ontime',         
        'is_dashboard_ontime',
        'bukti_report_gps',      
        'bukti_report_dashboard',
        'catatan_manager',
        'validated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KegiatanDetail::class, 'daily_report_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    protected $casts = [
        'tanggal' => 'date',
    ];
}
