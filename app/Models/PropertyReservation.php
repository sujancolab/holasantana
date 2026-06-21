<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyReservation extends Model
{
    protected $fillable = [
        'property_id',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'guest_name',
        'telephone',
        'remarks',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
