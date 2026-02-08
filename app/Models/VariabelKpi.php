<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariabelKpi extends Model
{
    protected $table = 'variabel_kpis';
    protected $fillable = ['divisi_id', 'nama_variabel', 'input_type', 'bobot'];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function calculateScore($inputValue)
    {
        if ($this->input_type === 'boolean') {
            return filter_var($inputValue, FILTER_VALIDATE_BOOLEAN) ? $this->bobot : 0;
        }

        if ($this->input_type === 'number') {
            return (float) $inputValue * $this->bobot;
        }

        // Default untuk string atau tipe lain
        return $this->bobot;
    }
}
