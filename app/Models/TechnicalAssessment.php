<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalAssessment extends Model
{
    protected $table = 'technical_assessments';
    protected $fillable = [
        'user_id',
        'periode_bulan',
        'periode_tahun',
        'jumlah_soal',
        'jumlah_benar',
        'bukti_kuis',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
