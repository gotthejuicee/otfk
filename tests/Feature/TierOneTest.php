<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use Tests\TestCase;

class TierOneTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_carousel_with_multiple_banners(): void
    {
        Banner::query()->delete();

        foreach (['Перший', 'Другий'] as $i => $title) {
            Banner::create([
                'title' => $title,
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('aria-roledescription="carousel"', escape: false)
            ->assertSee('Попередній слайд')
            ->assertSee('Наступний слайд')
            ->assertDontSee('hero-ken-burns', escape: false);
    }

    public function test_home_header_nav_sits_flush_above_hero(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('bg-brand-900 xl:block', escape: false)
            ->assertSee('relative overflow-hidden bg-brand-950', escape: false);
    }

    public function test_single_banner_has_no_carousel_controls(): void
    {
        Banner::query()->delete();

        Banner::create([
            'title' => 'Єдиний банер',
            'is_published' => true,
        ]);

        $res = $this->get('/')->assertOk();

        $res->assertDontSee('aria-roledescription="carousel"', escape: false);
        $res->assertDontSee('Попередній слайд', escape: false);
        $res->assertSee('Єдиний банер');
    }

    public function test_banner_image_alt_uses_title_fallback(): void
    {
        Banner::query()->delete();

        Banner::create([
            'title' => 'День відкритих дверей',
            'image' => 'banners/test.jpg',
            'is_published' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('alt="День відкритих дверей"', escape: false);
    }

    public function test_menu_navigation_is_cached(): void
    {
        Cache::flush();

        MenuItem::create([
            'label' => 'Новини',
            'link_type' => 'route',
            'url' => 'news.index',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $this->assertFalse(Cache::has('menu.navigation'));

        $this->get('/')->assertOk();

        $this->assertTrue(Cache::has('menu.navigation'));
        $this->assertGreaterThanOrEqual(1, MenuItem::navigation()->count());
    }
}