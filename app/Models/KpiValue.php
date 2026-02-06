<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiValue extends Model
{
    protected $fillable = ['kpi_case_id', 'kpi_variable_id', 'value'];

    public function kpiCase(): BelongsTo
    {
        return $this->belongsTo(KpiCase::class);
    }

    public function kpiVariable(): BelongsTo
    {
        return $this->belongsTo(KpiVariable::class);
    }
}