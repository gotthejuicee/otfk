<?php

namespace App\Support;

/**
 * Довідник святкових тем сайту. Тема обирається в адмінці
 * («Підвал і вигляд» → «Святкова тема», ключ settings `holiday_theme`)
 * і вмикає прикраси в публічному layout: святкову стрічку над шапкою,
 * падаючі частинки та бейдж біля логотипа (компонент <x-holiday-decor>).
 *
 * Порожнє/невідоме значення = звичайний вигляд без прикрас.
 */
class HolidayTheme
{
    /**
     * Конфігурація тем: label — назва в адмінці, badge — емодзі біля логотипа,
     * ribbon — CSS-фон святкової стрічки, particles — символи «снігопаду»
     * (порожній масив — без анімації), mono — частинки моноширинним шрифтом
     * (для «цифрових» тем на кшталт Дня програміста).
     *
     * @return array<string, array{label: string, badge: string, ribbon: string, particles: array<int, string>, mono?: bool}>
     */
    public static function all(): array
    {
        return [
            'new_year' => [
                'label' => 'Новорічна',
                'badge' => '🎄',
                'ribbon' => 'repeating-linear-gradient(135deg, #c62828 0 18px, #f8fafc 18px 36px, #2e7d32 36px 54px)',
                'particles' => ['❄️', '✨', '❄️', '⛄', '❄️', '🎄'],
            ],
            'vyshyvanka' => [
                'label' => 'День вишиванки',
                'badge' => '🌻',
                'ribbon' => 'repeating-linear-gradient(90deg, #b91c1c 0 12px, #111827 12px 18px, #b91c1c 18px 30px, #f8fafc 30px 36px)',
                'particles' => ['🌻', '🌾', '❤️'],
            ],
            'independence' => [
                'label' => 'День Незалежності',
                'badge' => '🇺🇦',
                'ribbon' => 'linear-gradient(180deg, #0057b7 0 50%, #ffd700 50% 100%)',
                'particles' => ['💙', '💛'],
            ],
            'halloween' => [
                'label' => 'Хелловін',
                'badge' => '🎃',
                'ribbon' => 'repeating-linear-gradient(135deg, #ea580c 0 18px, #1c1917 18px 36px)',
                'particles' => ['🎃', '👻', '🦇'],
            ],
            'teachers' => [
                'label' => 'День працівників освіти',
                'badge' => '📚',
                'ribbon' => 'linear-gradient(90deg, #b45309, #f59e0b, #b45309)',
                'particles' => ['🍁', '🍂', '📖'],
            ],
            'programmer' => [
                'label' => 'День програміста (13 вересня)',
                'badge' => '💻',
                'ribbon' => 'repeating-linear-gradient(90deg, #0f172a 0 26px, #14532d 26px 52px)',
                'particles' => ['0', '1', '</>', '{ }', '01'],
                'mono' => true,
            ],
            'energy' => [
                'label' => 'День енергетика (22 грудня)',
                'badge' => '⚡',
                'ribbon' => 'linear-gradient(90deg, #713f12, #facc15, #713f12)',
                'particles' => ['⚡', '💡'],
            ],
            'food' => [
                'label' => 'День харчовика',
                'badge' => '🥐',
                'ribbon' => 'linear-gradient(90deg, #9a3412, #fbbf24, #9a3412)',
                'particles' => ['🍞', '🥐', '🍎'],
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
