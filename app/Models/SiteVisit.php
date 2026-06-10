<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    public $timestamps = false;

    protected $fillable = ['date', 'path', 'hits'];

    /** Шлях-агрегат для «візитів» (унікальних сесій за день). */
    public const VISITS_PATH = '_visits';

    /** Інкремент лічильника «дата+шлях» (insert або +1). */
    public static function hit(string $path, ?string $date = null): void
    {
        $date = $date ?: now()->toDateString();

        $updated = static::query()
            ->where('date', $date)
            ->where('path', $path)
            ->increment('hits');

        if ($updated === 0) {
            try {
                static::create(['date' => $date, 'path' => $path, 'hits' => 1]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                static::query()->where('date', $date)->where('path', $path)->increment('hits');
            }
        }
    }
}
