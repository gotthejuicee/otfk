<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Разові слепки несохранённой формы адмінки для превʼю «як у GitHub wiki»:
 * стан форми кладеться в кеш під випадковим токеном, публічний шаблон
 * рендериться з цих даних без запису в БД. Слепок живе 10 хвилин.
 */
class AdminPreview
{
    private const TTL_MINUTES = 10;

    /**
     * Зберегти слепок: атрибути запису (якщо редагується наявний) поверх
     * перекриваються скалярними значеннями з форми. Файли й інші нескалярні
     * стани (FileUpload тощо) пропускаються — для них лишається збережене значення.
     */
    public static function store(string $type, Model $base, array $state): string
    {
        $attributes = $base->getAttributes();

        foreach ($state as $key => $value) {
            if ($base->isFillable($key) && (is_scalar($value) || $value === null)) {
                $attributes[$key] = $value;
            }
        }

        $token = Str::random(40);

        Cache::put(
            self::key($token),
            ['type' => $type, 'attributes' => $attributes],
            now()->addMinutes(self::TTL_MINUTES),
        );

        return $token;
    }

    /** @return array{type: string, attributes: array<string, mixed>}|null */
    public static function get(string $token): ?array
    {
        $snapshot = Cache::get(self::key($token));

        return is_array($snapshot) ? $snapshot : null;
    }

    private static function key(string $token): string
    {
        return 'admin-preview:' . $token;
    }
}
