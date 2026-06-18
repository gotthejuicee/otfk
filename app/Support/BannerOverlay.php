<?php

namespace App\Support;

use App\Models\Setting;

class BannerOverlay
{
    /** Колір brand-950 (#16223f) */
    private const RGB = '22, 34, 63';

    public static function strength(): int
    {
        return min(100, max(0, (int) (Setting::get('banner_overlay_opacity') ?? 75)));
    }

    public static function hasOverlay(): bool
    {
        return self::strength() > 0;
    }

    public static function gradientStyle(): string
    {
        $scale = self::strength() / 100;

        return sprintf(
            'background: linear-gradient(to right, rgba(%s, %s), rgba(%s, %s), rgba(%s, %s));',
            self::RGB,
            round(0.95 * $scale, 2),
            self::RGB,
            round(0.80 * $scale, 2),
            self::RGB,
            round(0.55 * $scale, 2),
        );
    }

    public static function flatStyle(): string
    {
        $scale = self::strength() / 100;

        return sprintf('background-color: rgba(%s, %s);', self::RGB, round(0.25 * $scale, 2));
    }
}