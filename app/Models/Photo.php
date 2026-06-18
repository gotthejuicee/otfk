<?php

namespace App\Models;

use App\Models\Concerns\OptimizesUploadedImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use OptimizesUploadedImages;

    /** @var list<string> */
    protected static array $optimizedImages = ['image'];

    protected $fillable = ['gallery_id', 'image', 'caption', 'sort_order'];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}
