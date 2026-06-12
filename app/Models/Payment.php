<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'amount',
        'status',
        'type',
        'reference_id',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
