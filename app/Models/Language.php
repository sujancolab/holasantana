<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function activeOptions(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'code')
            ->all() ?: ['en' => 'English', 'es' => 'Spanish'];
    }

    public static function defaultCode(): string
    {
        return (string) (static::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->value('code') ?: 'en');
    }
}
