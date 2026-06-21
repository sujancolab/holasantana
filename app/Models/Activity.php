<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'property_id',
        'visiting_at',
        'visitor_name',
        'observation',
        'activity_performed',
        'exit_time',
        'remarks',
    ];

    protected $casts = [
        'visiting_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
