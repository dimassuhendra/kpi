<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    protected $fillable = ['user_id', 'tanggal', 'total_nilai_harian', 'status', 'catatan_manager'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KegiatanDetail::class, 'daily_report_id');
    }
    
    protected $casts = [
        'tanggal' => 'date',
    ];
}
