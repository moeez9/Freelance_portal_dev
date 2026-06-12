<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigRequirement extends Model
{
    protected $fillable = ['gig_id', 'question', 'sort_order'];

    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
