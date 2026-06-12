<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigSubcategory extends Model
{
    protected $fillable = ['gig_category_id', 'name', 'slug'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(GigCategory::class, 'gig_category_id');
    }

    public function serviceTypes()
    {
        return $this->hasMany(GigServiceType::class);
    }
}
