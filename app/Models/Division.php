<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name', 'weighting_mode'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function kpiVariables()
    {
        return $this->hasMany(KpiVariable::class);
    }
}
