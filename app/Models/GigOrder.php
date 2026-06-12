<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GigOrder extends Model
{
    protected $fillable = [
        'gig_id',
        'gig_package_id',
        'package_id',
        'client_id',
        'status',
        'clarification_message',
        'payment_method',
        'payer_name',
        'payer_contact',
        'transaction_reference',
        'payment_verified_at',
        'payment_verified_by',
    ];

    protected $casts = [
        'payment_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $order) {
            if (!$order->slug) {
                $order->slug = static::generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }

    public function package()
    {
        $foreignKey = Schema::hasColumn($this->getTable(), 'gig_package_id') ? 'gig_package_id' : 'package_id';
        return $this->belongsTo(GigPackage::class, $foreignKey);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
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
