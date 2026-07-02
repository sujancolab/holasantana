<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayHome extends Model
{
    protected $fillable = [
        'area_name',
        'name',
        'image_url',
        'description',
        'number_of_bedrooms',
        'maximum_number_of_guests',
        'online_booking_link',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
