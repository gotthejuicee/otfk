<?php

namespace App\Models;

use App\Models\Concerns\OptimizesUploadedImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Specialty extends Model
{
    use OptimizesUploadedImages;

    /** @var list<string> */
    protected static array $optimizedImages = ['cover_image'];

    protected $fillable = [
        'title', 'slug', 'code', 'short_description', 'description',
        'degree', 'study_form', 'duration', 'cover_image', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class)->orderBy('sort_order');
    }

    /**
     * Декоративна іконка напряму підготовки — обкладинок у спеціальностей немає,
     * тож картки тримаються на коді та іконці. Спершу точний код, далі галузь знань
     * (перші дві цифри коду), інакше — загальна «академічна шапочка».
     */
    public function getIconNameAttribute(): string
    {
        $code = preg_replace('/\D/', '', (string) $this->code);

        return match ($code) {
            '121' => 'code-bracket',
            '123' => 'cpu-chip',
            '181' => 'beaker',
            '071' => 'calculator',
            default => match (substr($code, 0, 2)) {
                '12' => 'computer-desktop',
                '18' => 'beaker',
                '07' => 'chart-bar',
                default => 'academic-cap',
            },
        };
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public static function booted(): void
    {
        static::saving(function (Specialty $s) {
            if (blank($s->slug) && filled($s->title)) {
                $s->slug = Str::slug($s->title);
            }
        });
    }
}
