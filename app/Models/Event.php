<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'location', 'starts_at', 'ends_at', 'url', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /** Майбутні події (включно з тими, що тривають сьогодні). */
    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            $q->where('starts_at', '>=', now()->startOfDay())
                ->orWhere(fn ($qq) => $qq->whereNotNull('ends_at')->where('ends_at', '>=', now()));
        })->orderBy('starts_at');
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now()->startOfDay())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '<', now()))
            ->orderByDesc('starts_at');
    }
}
