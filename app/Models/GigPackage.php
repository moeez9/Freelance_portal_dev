<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigPackage extends Model
{
    protected $fillable = ['gig_id', 'type', 'name', 'description', 'price', 'revisions', 'delivery_days', 'delivery_time', 'features'];

    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
