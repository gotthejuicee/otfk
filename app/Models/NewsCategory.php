<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    protected $fillable = ['title', 'slug', 'sort_order', 'is_heritage'];

    protected function casts(): array
    {
        return [
            'is_heritage' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public static function booted(): void
    {
        static::saving(function (NewsCategory $category) {
            if (blank($category->slug) && filled($category->title)) {
                $category->slug = Str::slug($category->title);
            }
        });
    }
}
