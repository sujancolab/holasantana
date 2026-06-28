<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    protected $fillable = [
        'name',
        'telephone',
        'email',
        'whatsapp',
        'google_photo_album_link',
        'owner_user_id',
        'owner_password',
    ];

    protected $hidden = [
        'owner_password',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
