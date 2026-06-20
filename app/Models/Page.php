<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'menu_label',
        'meta_description',
        'hero_eyebrow',
        'hero_title',
        'hero_subtitle',
        'content_blocks',
        'template',
        'status',
        'show_in_menu',
        'menu_order',
    ];

    protected $casts = [
        'title' => 'array',
        'menu_label' => 'array',
        'meta_description' => 'array',
        'hero_eyebrow' => 'array',
        'hero_title' => 'array',
        'hero_subtitle' => 'array',
        'content_blocks' => 'array',
        'show_in_menu' => 'boolean',
    ];

    public function menuItem(): HasOne
    {
        return $this->hasOne(MenuItem::class);
    }

    public function localized(string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $value = $this->{$field};

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        return (string) ($value[$locale] ?? $value['en'] ?? collect($value)->first() ?? '');
    }
}
