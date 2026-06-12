<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Proposal extends Model
{
    protected $fillable = ['job_id', 'freelancer_id', 'cover_letter', 'cv_file_path', 'bid_amount', 'status', 'slug'];

    protected static function booted()
    {
        static::creating(function (self $proposal) {
            if (!$proposal->slug) {
                $proposal->slug = static::generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function getCvFileUrlAttribute(): ?string
    {
        if (!$this->cv_file_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->cv_file_path, '/'));
    }

    private static function generateUniqueSlug(): string
    {
        do {
            $slug = (string) Str::ulid();
            $exists = static::query()->where('slug', $slug)->exists();
        } while ($exists);

        return $slug;
    }
}
