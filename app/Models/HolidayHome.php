<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayHome extends Model
{
    protected $fillable = [
        'area_name',
        'name',
        'number_of_bedrooms',
        'maximum_number_of_guests',
        'online_booking_link',
    ];
}
