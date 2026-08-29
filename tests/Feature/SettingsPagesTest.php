<?php

namespace Tests\Feature;

use App\Filament\Pages\AnnouncementSettings;
use App\Filament\Pages\AppearanceSettings;
use App\Filament\Pages\ContactSettings;
use App\Filament\Pages\TelegramSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Етап 2 ADMIN-UX-PLAN: «людські» сторінки налаштувань замість сирого
 * key-value CRUD. Контракти: сторінки рендеряться, збереження пише в settings
 * і скидає кеш settings.map (обсервер у Setting::booted()), Telegram-тест
 * шле повідомлення з незбережених значень форми, SettingResource
 * перейменовано на «Розширені налаштування».
 */
class SettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::firstOrFail()); // створюється сидером
    }

    public function test_settings_pages_render(): void
    {
        $this->get(ContactSettings::getUrl())->assertOk()
            ->assertSee('Контактні дані')->assertSee('Соцмережі');
        $this->get(AnnouncementSettings::getUrl())->assertOk()
            ->assertSee('Смуга оголошення');
        $this->get(TelegramSettings::getUrl())->assertOk()
            ->assertSee('Токен бота');
        $this->get(AppearanceSettings::getUrl())->assertOk()
            ->assertSee('Позначка версії сайту');
    }

    public function test_contact_settings_save_and_reset_cache(): void
    {
        Setting::map(); // прогріваємо кеш — збереження має його скинути

        Livewire::test(ContactSettings::class)
            ->fillForm([
                'contact_phone' => '+38 (048) 111-22-33',
                'contact_email' => 'test@otfk.od.ua',
                'social_youtube' => 'https://www.youtube.com/@otfk',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('+38 (048) 111-22-33', Setting::get('contact_phone'));
        $this->assertSame('test@otfk.od.ua', Setting::get('contact_email'));
        $this->assertSame('https://www.youtube.com/@otfk', Setting::get('social_youtube'));
    }

    public function test_contact_settings_reject_invalid_social_url(): void
    {
        Livewire::test(ContactSettings::class)
            ->fillForm(['social_facebook' => 'не посилання'])
            ->call('save')
            ->assertHasFormErrors(['social_facebook']);
    }

    public function test_announcement_appears_on_site_after_save(): void
    {
        Livewire::test(AnnouncementSettings::class)
            ->fillForm([
                'announcement_text' => 'Тестове термінове оголошення',
                'announcement_type' => 'danger',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('danger', Setting::get('announcement_type'));

        $this->get('/')->assertOk()->assertSee('Тестове термінове оголошення');
    }

    public function test_telegram_toggle_stored_as_flag(): void
    {
        Livewire::test(TelegramSettings::class)
            ->fillForm(['telegram_autopost' => true, 'telegram_channel' => '@otfk_test'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('1', Setting::get('telegram_autopost'));
        $this->assertSame('@otfk_test', Setting::get('telegram_channel'));

        Livewire::test(TelegramSettings::class)
            ->assertFormSet(['telegram_autopost' => true])
            ->fillForm(['telegram_autopost' => false])
            ->call('save');

        $this->assertSame('0', Setting::get('telegram_autopost'));
    }

    public function test_telegram_test_message_uses_unsaved_form_values(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        Livewire::test(TelegramSettings::class)
            ->fillForm(['telegram_bot_token' => '42:TEST', 'telegram_channel' => '@kanal'])
            ->call('sendTest');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'bot42:TEST/sendMessage')
            && $request['chat_id'] === '@kanal');

        // У БД нічого не збережено — тест працює до «Зберегти»
        $this->assertNotSame('42:TEST', Setting::query()->where('key', 'telegram_bot_token')->value('value'));
    }

    public function test_telegram_test_message_requires_token_and_channel(): void
    {
        Http::fake();

        Livewire::test(TelegramSettings::class)
            ->fillForm(['telegram_bot_token' => '', 'telegram_channel' => ''])
            ->call('sendTest');

        Http::assertNothingSent();
    }

    public function test_appearance_settings_save(): void
    {
        Livewire::test(AppearanceSettings::class)
            ->fillForm([
                'footer_about' => 'Короткий текст про коледж.',
                'site_version_label' => 'Бета-версія',
                'site_version_color' => 'green',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Короткий текст про коледж.', Setting::get('footer_about'));
        $this->assertSame('Бета-версія', Setting::get('site_version_label'));
        $this->assertSame('green', Setting::get('site_version_color'));

        $this->get('/')->assertOk()->assertSee('Бета-версія');
    }

    public function test_appearance_page_syncs_footer_partners(): void
    {
        // Партнери підвалу тепер редагуються тут (репітер поверх quick_links
        // location=footer_partner): створення, оновлення за id, видалення прибраних.
        $old = \App\Models\QuickLink::create([
            'location' => 'footer_partner', 'title' => 'Старий партнер', 'url' => 'https://old.example',
        ]);
        $gone = \App\Models\QuickLink::create([
            'location' => 'footer_partner', 'title' => 'Зайвий партнер', 'url' => 'https://gone.example',
        ]);
        $tile = \App\Models\QuickLink::create([
            'location' => 'home_tile', 'title' => 'Плитка', 'url' => '/',
        ]);

        Livewire::test(AppearanceSettings::class)
            ->fillForm([
                'partners' => [
                    ['id' => null, 'title' => 'Новий партнер', 'url' => 'https://new.example', 'open_new_tab' => true, 'is_visible' => true],
                    ['id' => $old->id, 'title' => 'Оновлений партнер', 'url' => 'https://old.example', 'open_new_tab' => true, 'is_visible' => true],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $partners = \App\Models\QuickLink::location('footer_partner')->ordered()->get();

        $this->assertSame(['Новий партнер', 'Оновлений партнер'], $partners->pluck('title')->all());
        $this->assertSame([0, 1], $partners->pluck('sort_order')->all());
        $this->assertNull(\App\Models\QuickLink::find($gone->id));
        $this->assertNotNull($tile->fresh()); // плиток головної синхронізація не торкається
    }

    public function test_raw_setting_resource_is_renamed_to_advanced(): void
    {
        $this->get('/admin/settings')->assertOk()
            ->assertSee('Розширені налаштування')
            ->assertSee('аварійний випадок');
    }
}
