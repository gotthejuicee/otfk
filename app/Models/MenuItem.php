<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'parent_id', 'label', 'link_type', 'page_id', 'url',
        'open_new_tab', 'sort_order', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'open_new_tab' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->visible()->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * Обчислене посилання пункту меню.
     */
    public function getHrefAttribute(): string
    {
        return match ($this->link_type) {
            'url' => $this->url ?: '#',
            'route' => $this->url && \Illuminate\Support\Facades\Route::has($this->url) ? route($this->url) : '#',
            default => $this->page ? url('/' . $this->page->slug) : ($this->url ?: '#'),
        };
    }
}
