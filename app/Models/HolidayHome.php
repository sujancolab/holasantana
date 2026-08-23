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

    public function getImageSrcAttribute(): string
    {
        $imageUrl = trim((string) $this->image_url);

        if ($imageUrl === '') {
            return '';
        }

        $localStoragePath = parse_url($imageUrl, PHP_URL_PATH);

        if (
            is_string($localStoragePath)
            && str_starts_with($localStoragePath, '/storage/')
            && preg_match('#^https?://(?:localhost|127\.0\.0\.1)(?::\d+)?/#', $imageUrl)
        ) {
            return $localStoragePath;
        }

        if (preg_match('#^https?://#i', $imageUrl) || str_starts_with($imageUrl, '//')) {
            return $imageUrl;
        }

        if (str_starts_with($imageUrl, '/storage/')) {
            return $imageUrl;
        }

        if (str_starts_with($imageUrl, 'storage/')) {
            return '/' . $imageUrl;
        }

        if (str_starts_with($imageUrl, '/')) {
            return $imageUrl;
        }

        return '/storage/' . ltrim($imageUrl, '/');
    }
}
