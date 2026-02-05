<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'assessment_date',
        'total_final_score',
        'status',
        'approved_by',
        'approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(KpiDetail::class);
    }

    public function caseLogs()
    {
        return $this->hasMany(KpiCaseLog::class);
    }
}
