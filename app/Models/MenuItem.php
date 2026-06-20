<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = [
        'page_id',
        'label',
        'url',
        'sort_order',
        'is_active',
        'target',
    ];

    protected $casts = [
        'label' => 'array',
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function localizedLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return (string) ($this->label[$locale] ?? $this->label['en'] ?? collect($this->label)->first() ?? '');
    }

    public function href(?string $locale = null): string
    {
        if ($this->url) {
            return $this->url;
        }

        if (! $this->page) {
            return '#';
        }

        return route('pages.show', ['locale' => $locale ?? app()->getLocale(), 'slug' => $this->page->slug]);
    }
}
