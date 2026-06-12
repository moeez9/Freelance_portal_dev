<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigServiceType extends Model
{
    protected $fillable = ['gig_subcategory_id', 'name', 'slug'];

    public function subcategory()
    {
        return $this->belongsTo(GigSubcategory::class, 'gig_subcategory_id');
    }
}
