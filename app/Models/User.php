<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    protected $fillable = ['username', 'password', 'nama_lengkap', 'role', 'divisi_id' , 'email'];
    protected $hidden = ['password', 'remember_token'];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function reports()
    {
        return $this->hasMany(DailyReport::class, 'user_id');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'user_id');
    }

    public function latestReport()
    {
        return $this->hasOne(DailyReport::class)->latestOfMany();
    }

    // Relasi ke KegiatanDetail MELALUI DailyReport untuk countcase
    public function details(): HasManyThrough
    {
        return $this->hasManyThrough(
            KegiatanDetail::class,
            DailyReport::class,
            'user_id',          // Foreign key di tabel daily_reports
            'daily_report_id',  // Foreign key di tabel kegiatan_detail (SESUAI SQL LU)
            'id',               // Local key di tabel users
            'id'                // Local key di tabel daily_reports
        );
    }
}
