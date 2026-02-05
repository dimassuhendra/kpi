<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiCaseLog extends Model
{
    protected $fillable = [
        'kpi_submission_id',
        'ticket_number',
        'response_time_minutes',
        'is_problem_detected_by_staff'
    ];

    public function submission()
    {
        // Tambahkan parameter kedua yaitu 'kpi_submission_id'
        return $this->belongsTo(KpiSubmission::class, 'kpi_submission_id');
    }
}
