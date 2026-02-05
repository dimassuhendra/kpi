<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiVariable extends Model
{
    protected $fillable = ['division_id', 'variable_name', 'weight', 'input_type', 'scoring_matrix'];

    protected $casts = [
        'scoring_matrix' => 'array',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
