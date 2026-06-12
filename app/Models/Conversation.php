<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    protected $fillable = [
        'slug',
        'context_type',
        'context_id',
        'created_by',
    ];

    protected static function booted()
    {
        static::creating(function (self $conversation) {
            if (!$conversation->slug) {
                $conversation->slug = static::generateUniqueSlug();
            }
        });
    }

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
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
