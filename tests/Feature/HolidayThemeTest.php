<?php

namespace Tests\Feature;

use App\Filament\Pages\AppearanceSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\HolidayTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Святкові теми сайту (ключ settings `holiday_theme`, довідник
 * App\Support\HolidayTheme). Контракти: без теми прикрас немає; активна тема
 * рендерить стрічку, частинки та бейдж біля логотипа; невідоме значення
 * ігнорується; тема вмикається й вимикається зі сторінки «Підвал і вигляд».
 */
class HolidayThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_decorations_by_default(): void
    {
        $this->get('/')->assertOk()
            ->assertDontSee('holiday-decor')
            ->assertDontSee('holiday-logo-badge');
    }

    public function test_active_theme_renders_decorations_and_logo_badge(): void
    {
        Setting::updateOrCreate(['key' => 'holiday_theme'], ['value' => 'new_year', 'group' => 'appearance']);

        $this->get('/')->assertOk()
            ->assertSee('data-holiday="new_year"', false)
            ->assertSee('holiday-ribbon')
            ->assertSee('holiday-particles')
            ->assertSee('holiday-logo-badge')
            ->assertSee('🎄');
    }

    public function test_unknown_theme_value_is_ignored(): void
    {
        Setting::updateOrCreate(['key' => 'holiday_theme'], ['value' => 'no-such-theme', 'group' => 'appearance']);

        $this->get('/')->assertOk()->assertDontSee('holiday-decor');
    }

    public function test_every_theme_has_complete_config_and_renders(): void
    {
        foreach (HolidayTheme::all() as $key => $theme) {
            $this->assertNotSame('', $theme['label'], "Тема {$key} без назви");
            $this->assertNotSame('', $theme['badge'], "Тема {$key} без бейджа");
            $this->assertNotSame('', $theme['ribbon'], "Тема {$key} без стрічки");

            Setting::updateOrCreate(['key' => 'holiday_theme'], ['value' => $key, 'group' => 'appearance']);

            $this->get('/')->assertOk()->assertSee('data-holiday="' . $key . '"', false);
        }
    }

    public function test_theme_saved_and_cleared_from_admin_page(): void
    {
        $this->actingAs(User::firstOrFail()); // створюється сидером

        Livewire::test(AppearanceSettings::class)
            ->fillForm(['holiday_theme' => 'halloween'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('halloween', Setting::get('holiday_theme'));
        $this->get('/')->assertOk()->assertSee('data-holiday="halloween"', false);

        Livewire::test(AppearanceSettings::class)
            ->assertFormSet(['holiday_theme' => 'halloween'])
            ->fillForm(['holiday_theme' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('', (string) Setting::get('holiday_theme'));
        $this->get('/')->assertOk()->assertDontSee('holiday-decor');
    }
}
