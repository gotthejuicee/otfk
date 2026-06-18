<?php

namespace App\Models;

use App\Models\Concerns\OptimizesUploadedImages;
use App\Observers\NewsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[ObservedBy(NewsObserver::class)]
class News extends Model
{
    use OptimizesUploadedImages;

    /** @var list<string> */
    protected static array $optimizedImages = ['cover_image'];

    protected $table = 'news';

    protected $fillable = [
        'category_id', 'title', 'slug', 'excerpt', 'body', 'cover_image',
        'published_at', 'is_published', 'is_featured', 'views', 'likes',
        'telegram_posted_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'telegram_posted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function likeRecords()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public static function booted(): void
    {
        static::saving(function (News $news) {
            if (blank($news->slug) && filled($news->title)) {
                $news->slug = Str::slug($news->title);
            }
        });

        // Автопостинг у Telegram живе в NewsObserver (винесено з циклу збереження).
    }
}
