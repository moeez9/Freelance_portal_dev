<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone_no',
        'password',
        'role',
        'dob',
        'gender',
        'profile_pic',
        'candidate_payment_method',
        'candidate_payment_details',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (blank($user->slug)) {
                $user->slug = static::generateUniqueNameSlug($user->name);
            }
        });

        static::updating(function (self $user) {
            if ($user->isDirty('name')) {
                $user->slug = static::generateUniqueNameSlug($user->name, $user->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        $field = $field ?? $this->getRouteKeyName();

        $model = $this->where($field, $value)->first();
        if ($model) {
            return $model;
        }

        if ($field === 'slug' && is_numeric($value)) {
            $fallback = $this->whereKey((int) $value)->first();
            if ($fallback) {
                return $fallback;
            }
        }

        throw (new ModelNotFoundException())->setModel(static::class, [$value]);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function gigs()
    {
        return $this->hasMany(Gig::class, 'freelancer_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    public function gigOrders()
    {
        return $this->hasMany(GigOrder::class, 'client_id');
    }

    public function conversations()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function notificationsList()
    {
        return $this->hasMany(UserNotification::class);
    }

    private static function generateUniqueNameSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'user';
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
