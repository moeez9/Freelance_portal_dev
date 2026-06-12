<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkSubmission extends Model
{
    protected $fillable = [
        'job_id',
        'freelancer_id',
        'content',
        'file_path',
        'status',
        'slug',
    ];

    protected static function booted()
    {
        static::creating(function (self $submission) {
            if (!$submission->slug) {
                $submission->slug = static::generateUniqueSlug();
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

    private static function generateUniqueSlug(): string
    {
        do {
            $slug = (string) Str::ulid();
            $exists = static::query()->where('slug', $slug)->exists();
        } while ($exists);

        return $slug;
    }
}
