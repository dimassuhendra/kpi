<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    protected $table = 'customer_feedbacks';
    protected $fillable = [
        'user_id',
        'nomor_tiket',
        'tanggal_survey',
        'nama_pelanggan',
        'rating',
        'bukti_survey',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
