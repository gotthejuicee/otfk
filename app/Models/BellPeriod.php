<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BellPeriod extends Model
{
    /** Ключ налаштування-перемикача другої зміни. */
    public const SECOND_SHIFT_SETTING = 'bells_second_shift';

    protected $fillable = ['number', 'shift', 'starts', 'ends', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'shift' => 'integer', 'number' => 'integer'];
    }

    /**
     * Активні пари у порядку змін і номерів (кешовано — макет показує їх на кожній сторінці).
     * Друга зміна ховається перемикачем у налаштуваннях, самі пари при цьому лишаються в базі.
     */
    public static function active()
    {
        // .v2 — у кеші тепер є колонка shift; старий ключ міг лишитися на проді.
        $periods = Cache::remember('bell_periods.v2', 600, fn () => static::query()
            ->where('is_active', true)
            ->orderBy('shift')
            ->orderBy('number')
            ->get(['number', 'shift', 'starts', 'ends']));

        return static::secondShiftEnabled() ? $periods : $periods->where('shift', 1)->values();
    }

    /** Чи показувати другу зміну на сайті (налаштування `bells_second_shift`, типово — так). */
    public static function secondShiftEnabled(): bool
    {
        return Setting::get(self::SECOND_SHIFT_SETTING, '1') === '1';
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('bell_periods.v2'));
        static::deleted(fn () => Cache::forget('bell_periods.v2'));
    }
}
