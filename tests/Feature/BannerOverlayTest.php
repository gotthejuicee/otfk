<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerOverlayTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_banners_page_renders_overlay_control(): void
    {
        $admin = \App\Models\User::firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/banners')
            ->assertOk()
            ->assertSee('Затемнення фото')
            ->assertSee('Сила затемнення');
    }

    public function test_banner_overlay_strength_follows_setting(): void
    {
        Banner::query()->delete();

        Banner::create([
            'title' => 'Тестовий банер',
            'image' => 'banners/test.jpg',
            'is_published' => true,
        ]);

        Setting::updateOrCreate(
            ['key' => 'banner_overlay_opacity'],
            ['value' => '0', 'group' => 'appearance', 'type' => 'number'],
        );
        cache()->forget('settings.map');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('linear-gradient(to right, rgba(22, 34, 63', escape: false);

        Setting::updateOrCreate(
            ['key' => 'banner_overlay_opacity'],
            ['value' => '50', 'group' => 'appearance', 'type' => 'number'],
        );
        cache()->forget('settings.map');

        $this->get('/')
            ->assertOk()
            ->assertSee('rgba(22, 34, 63, 0.48)', escape: false);
    }
}