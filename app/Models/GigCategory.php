<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function subcategories()
    {
        return $this->hasMany(GigSubcategory::class);
    }
}
