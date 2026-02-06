<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiVariable extends Model
{
    protected $fillable = ['division_id', 'variable_name', 'weight', 'is_bonus', 'input_type'];

    protected $casts = [
        'is_bonus' => 'boolean',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
