<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';
    protected $fillable = [
        'id',
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'created_at',
        'updated_at'
    ];
}
