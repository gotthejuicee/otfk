<?php

namespace App\Support;

/**
 * Довідник святкових тем сайту. Тема обирається в адмінці
 * («Підвал і вигляд» → «Святкова тема», ключ settings `holiday_theme`)
 * і вмикає прикраси в публічному layout (компонент <x-holiday-decor>):
 * перефарбовує темний «хром» сайту (верхня стрічка, навігація, підвал)
 * у кольори свята, малює святкову стрічку та гірлянду над шапкою,
 * бейдж біля логотипа, великі кутові емодзі та короткий «залп»
 * падаючих частинок при завантаженні сторінки.
 *
 * Порожнє/невідоме значення = звичайний вигляд без прикрас.
 */
class HolidayTheme
{
    /**
     * Конфігурація тем:
     *  - label     — назва в адмінці;
     *  - badge     — емодзі біля логотипа;
     *  - ribbon    — CSS-фон святкової стрічки над шапкою;
     *  - chrome    — фон навігаційної стрічки замість темно-синього;
     *  - chrome_dark — фон верхньої смуги та підвалу;
     *  - accent    — акцент (верхня межа підвалу);
     *  - particles — символи «залпу» при завантаженні (порожньо — без нього);
     *  - mono      — частинки моноширинним шрифтом («цифрові» теми);
     *  - garland   — рядок емодзі гірлянди під стрічкою (необовʼязково);
     *  - corners   — до двох великих емодзі в нижніх кутах екрана (необовʼязково).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'new_year' => [
                'label' => 'Новорічна',
                'badge' => '🎄',
                'ribbon' => 'repeating-linear-gradient(135deg, #c62828 0 18px, #f8fafc 18px 36px, #2e7d32 36px 54px)',
                'chrome' => 'linear-gradient(180deg, #0e4d3a, #0b3d2e)',
                'chrome_dark' => '#062b20',
                'accent' => '#e11d48',
                'particles' => ['❄️', '✨', '❄️', '⛄', '❄️', '🎄'],
                'garland' => '🔴🟡🟢🔵',
                'corners' => ['🎄', '⛄'],
            ],
            'vyshyvanka' => [
                'label' => 'День вишиванки',
                'badge' => '🌻',
                'ribbon' => 'repeating-linear-gradient(90deg, #b91c1c 0 12px, #111827 12px 18px, #b91c1c 18px 30px, #f8fafc 30px 36px)',
                'chrome' => 'linear-gradient(180deg, #991b1b, #7f1d1d)',
                'chrome_dark' => '#450a0a',
                'accent' => '#f8fafc',
                'particles' => ['🌻', '🌾', '❤️'],
                'garland' => '❋✕❋',
                'corners' => ['🌻'],
            ],
            'independence' => [
                'label' => 'День Незалежності',
                'badge' => '🇺🇦',
                'ribbon' => 'linear-gradient(180deg, #0057b7 0 50%, #ffd700 50% 100%)',
                'chrome' => 'linear-gradient(180deg, #004aa0, #003f8a)',
                'chrome_dark' => '#00295c',
                'accent' => '#ffd700',
                'particles' => ['💙', '💛'],
                'garland' => '🇺🇦',
                'corners' => ['🇺🇦'],
            ],
            'halloween' => [
                'label' => 'Хелловін',
                'badge' => '🎃',
                'ribbon' => 'repeating-linear-gradient(135deg, #ea580c 0 18px, #1c1917 18px 36px)',
                'chrome' => 'linear-gradient(180deg, #52200a, #431407)',
                'chrome_dark' => '#1c0a02',
                'accent' => '#f97316',
                'particles' => ['🎃', '👻', '🦇'],
                'garland' => '🎃👻🦇',
                'corners' => ['🎃', '🕸️'],
            ],
            'teachers' => [
                'label' => 'День працівників освіти',
                'badge' => '📚',
                'ribbon' => 'linear-gradient(90deg, #b45309, #f59e0b, #b45309)',
                'chrome' => 'linear-gradient(180deg, #8a4210, #78350f)',
                'chrome_dark' => '#451a03',
                'accent' => '#fbbf24',
                'particles' => ['🍁', '🍂', '📖'],
                'corners' => ['🍁'],
            ],
            'programmer' => [
                'label' => 'День програміста (13 вересня)',
                'badge' => '💻',
                'ribbon' => 'repeating-linear-gradient(90deg, #0f172a 0 26px, #14532d 26px 52px)',
                'chrome' => 'linear-gradient(180deg, #101826, #0a0f1a)',
                'chrome_dark' => '#05080f',
                'accent' => '#22c55e',
                'particles' => ['0', '1', '</>', '{ }', '01'],
                'mono' => true,
                'corners' => ['💻'],
            ],
            'energy' => [
                'label' => 'День енергетика (22 грудня)',
                'badge' => '⚡',
                'ribbon' => 'linear-gradient(90deg, #713f12, #facc15, #713f12)',
                'chrome' => 'linear-gradient(180deg, #854d0e, #713f12)',
                'chrome_dark' => '#422006',
                'accent' => '#facc15',
                'particles' => ['⚡', '💡'],
                'corners' => ['⚡'],
            ],
            'food' => [
                'label' => 'День харчовика',
                'badge' => '🥐',
                'ribbon' => 'linear-gradient(90deg, #9a3412, #fbbf24, #9a3412)',
                'chrome' => 'linear-gradient(180deg, #8c3512, #7c2d12)',
                'chrome_dark' => '#431407',
                'accent' => '#fdba74',
                'particles' => ['🍞', '🥐', '🍎'],
                'corners' => ['🥐', '🍎'],
            ],
        ];
    }

    /** Конфігурація активної теми або null для звичайного вигляду. */
    public static function config(?string $key): ?array
    {
        return $key ? (static::all()[$key] ?? null) : null;
    }

    /**
     * Варіанти для Select в адмінці (порожній ключ = звичайна тема).
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return ['' => 'Звичайна (без прикрас)']
            + array_map(fn (array $theme) => $theme['label'], static::all());
    }
}
