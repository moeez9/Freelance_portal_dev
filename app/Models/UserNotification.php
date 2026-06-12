<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'slug',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $notification) {
            if (!$notification->slug) {
                $notification->slug = static::generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
