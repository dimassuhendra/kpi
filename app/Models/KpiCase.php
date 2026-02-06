<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCase extends Model
{
    protected $fillable = ['user_id', 'case_title', 'description', 'entry_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kpiValues(): HasMany
    {
        return $this->hasMany(KpiValue::class);
    }
}
