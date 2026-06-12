<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gig extends Model
{
    protected $fillable = [
        'freelancer_id', 
        'title', 
        'slug',
        'description', 
        'gig_category_id',
        'gig_subcategory_id',
        'gig_service_type_id',
        'search_tags',
        'thumbnail', 
        'gallery',
        'video_path',
        'document_paths',
        'status'
    ];

    protected $casts = [
        'search_tags' => 'array',
        'requirement_questions' => 'array',
        'gallery' => 'array',
        'document_paths' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $gig) {
            $gig->slug = static::generateUniqueTitleSlug($gig->title);
        });

        static::updating(function (self $gig) {
            if ($gig->isDirty('title')) {
                $gig->slug = static::generateUniqueTitleSlug($gig->title, $gig->id);
            }
        });
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function gigCategory()
    {
        return $this->belongsTo(GigCategory::class, 'gig_category_id');
    }

    public function gigSubcategory()
    {
        return $this->belongsTo(GigSubcategory::class, 'gig_subcategory_id');
    }

    public function gigServiceType()
    {
        return $this->belongsTo(GigServiceType::class, 'gig_service_type_id');
    }

    public function packages()
    {
        return $this->hasMany(GigPackage::class);
    }

    public function requirements()
    {
        return $this->hasMany(GigRequirement::class)->orderBy('sort_order');
    }

    public function orders()
    {
        return $this->hasMany(GigOrder::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->gigCategory?->name ?? $this->getRawOriginal('category');
    }

    public function getSubCategoryAttribute(): ?string
    {
        return $this->gigSubcategory?->name ?? $this->getRawOriginal('sub_category');
    }

    public function getServiceTypeAttribute(): ?string
    {
        return $this->gigServiceType?->name ?? $this->getRawOriginal('service_type');
    }

    private static function generateUniqueTitleSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'gig';
        }

        do {
            $slug = $base . '-' . Str::random(8);
            $exists = static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists();
        } while ($exists);

        return $slug;
    }
}
