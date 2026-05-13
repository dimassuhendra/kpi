<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingNote extends Model
{
    use HasFactory;

    protected $table = 'daily_meeting_notes';


    protected $fillable = [
        'daily_report_id',
        'user_id',
        'judul_briefing',
        'isi_notulen',
    ];

    /**
     * Relasi ke Laporan Harian Utama
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    /**
     * Relasi ke User yang membuat notulen
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
