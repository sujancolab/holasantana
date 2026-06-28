<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    public const TYPES = [
        'Tourist Rental',
        'Seasonal Home',
        'Hotel',
        'Restaurant',
        'Housing Community',
        'Office',
        'Industrial Property',
        'Others',
    ];

    protected $fillable = [
        'name',
        'type',
        'other_type',
        'address',
        'owner_id',
        'laundry_included',
        'check_in_included',
        'cleaning_included',
        'management_included',
        'full_service_included',
        'price_per_service',
        'annual_price',
        'remarks',
    ];

    protected $casts = [
        'laundry_included' => 'boolean',
        'check_in_included' => 'boolean',
        'cleaning_included' => 'boolean',
        'management_included' => 'boolean',
        'full_service_included' => 'boolean',
        'price_per_service' => 'decimal:2',
        'annual_price' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(PropertyReservation::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
