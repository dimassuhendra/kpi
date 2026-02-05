<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiDetail extends Model
{
    protected $fillable = ['kpi_submission_id', 'kpi_variable_id', 'staff_value', 'manager_correction', 'calculated_score'];

    public function variable()
    {
        return $this->belongsTo(KpiVariable::class, 'kpi_variable_id');
    }
}
