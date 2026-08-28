<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\StatItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Контракти мобільної шапки та мобільного вигляду героя.
 * Мобільний трафік абітурієнтів — основний, тому ці рішення пришиті тестами.
 */
class MobileHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_cta_stays_in_header_on_phones(): void
    {
        $this->get('/')
            ->assertOk()
            // Компактний варіант кнопки, видимий з найменшої ширини
            ->assertSee('btn-accent group h-11 whitespace-nowrap px-3 text-xs sm:h-auto sm:px-5 sm:text-sm', escape: false)
            // Старий варіант (кнопка зʼявлялась лише від sm) більше не повертати
            ->assertDontSee('btn-accent group hidden whitespace-nowrap sm:inline-flex', escape: false);
    }

    public function test_burger_is_wired_to_offcanvas_menu(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="mobile-menu"', escape: false)
            ->assertSee('aria-controls="mobile-menu"', escape: false)
            ->assertSee(':aria-expanded="mobile ? \'true\' : \'false\'"', escape: false)
            // Фон під відкритим меню не має скролитись
            ->assertSee("document.body.classList.toggle('overflow-hidden', mobile)", escape: false)
            ->assertSee('@keydown.escape.window="mobile = false"', escape: false);
    }

    public function test_offcanvas_menu_carries_quick_actions_and_contacts(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Три найчастіші дії з телефона
        $this->assertStringContainsString('Дзвінки', $html);
        $this->assertStringContainsString(route('bells'), $html);
        $this->assertStringContainsString(route('contacts'), $html);

        // Утилітарна стрічка схована на мобільному — телефон і пошта дублюються в шухляді
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'href="tel:'));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'href="mailto:'));
    }

    public function test_hero_shows_stat_strip_under_slides_on_phones(): void
    {
        Banner::query()->delete();
        Banner::create(['title' => 'Вступ 2026', 'is_published' => true, 'sort_order' => 0]);
        Banner::create(['title' => 'Спорт', 'is_published' => true, 'sort_order' => 1]);

        StatItem::query()->delete();
        StatItem::create(['label' => 'Студентів', 'value' => '1000+', 'icon' => 'users', 'is_active' => true, 'sort_order' => 0]);

        $res = $this->get('/')->assertOk();

        // Чипи всередині слайда лишаються лише від sm
        $res->assertSee('mt-10 hidden flex-wrap gap-3 sm:flex', escape: false);
        // А на телефоні факти йдуть окремою стрічкою під сценою слайдів
        $res->assertSee('border-t border-white/10 sm:hidden', escape: false);
        // Показник видно у двох слайдах-чипах, у мобільній стрічці та в «Коледж у цифрах»
        $this->assertGreaterThanOrEqual(3, substr_count($res->getContent(), 'Студентів'));
    }

    public function test_carousel_dots_have_touch_sized_targets(): void
    {
        Banner::query()->delete();
        Banner::create(['title' => 'Перший', 'is_published' => true, 'sort_order' => 0]);
        Banner::create(['title' => 'Другий', 'is_published' => true, 'sort_order' => 1]);

        $this->get('/')
            ->assertOk()
            ->assertSee('grid h-11 w-8 place-items-center', escape: false);
    }
}
