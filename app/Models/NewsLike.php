<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsLike extends Model
{
    protected $fillable = ['news_id', 'fingerprint'];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
