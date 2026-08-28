<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Редизайн контактів: світла шапка з швидкими діями, картки контактів із
 * налаштувань, форма зворотного звʼязку зі станами помилки/успіху та карта.
 */
class ContactsPageTest extends TestCase
{
    use RefreshDatabase;

    private function setContacts(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'contacts', 'type' => 'text']);
        }

        cache()->forget('settings.map');
    }

    private function clearContacts(): void
    {
        Setting::whereIn('key', ['contact_address', 'contact_phone', 'contact_email', 'work_hours', 'map_embed', 'social_facebook', 'social_instagram', 'social_youtube'])
            ->update(['value' => '']);

        cache()->forget('settings.map');
    }

    public function test_light_header_offers_quick_actions_from_settings(): void
    {
        $this->setContacts([
            'contact_phone' => '(048) 753-16-51',
            'contact_address' => 'вул. Балківська, 54, Одеса',
        ]);

        $this->get('/kontakty')
            ->assertOk()
            // Світла шапка замість navy-героя
            ->assertSee('border-b border-slate-200/70 bg-slate-50/80', false)
            ->assertSee('Звʼяжіться з коледжем', false)
            // Телефон клікабельний, адреса веде на карти
            ->assertSee('tel:0487531651', false)
            ->assertSee('Прокласти маршрут')
            ->assertSee('href="#forma"', false);
    }

    public function test_contact_cards_come_from_settings(): void
    {
        $this->setContacts([
            'contact_email' => 'otkua@ukr.net',
            'work_hours' => 'Пн–Пт, 8:00–17:00',
        ]);

        $this->get('/kontakty')
            ->assertOk()
            ->assertSee('mailto:otkua@ukr.net', false)
            ->assertSee('Пн–Пт, 8:00–17:00', false)
            ->assertSee('Графік роботи');
    }

    public function test_empty_settings_hide_contact_blocks(): void
    {
        $this->clearContacts();

        $response = $this->get('/kontakty')->assertOk();

        $response->assertDontSee('Графік роботи');
        $response->assertDontSee('Прокласти маршрут');
        $response->assertDontSee('Як нас знайти');
        $response->assertDontSee('Ми в соцмережах');
        // Сама форма лишається — це головна дія сторінки
        $response->assertSee('Надіслати звернення');
    }

    public function test_map_block_renders_when_embed_is_set(): void
    {
        $this->setContacts([
            'map_embed' => 'https://www.google.com/maps/embed?pb=test',
            'contact_address' => 'вул. Балківська, 54, Одеса',
        ]);

        $this->get('/kontakty')
            ->assertOk()
            ->assertSee('Як нас знайти')
            ->assertSee('Карта розташування коледжу', false)
            ->assertSee('Відкрити в Google Maps');
    }

    public function test_validation_errors_show_summary_and_keep_input(): void
    {
        $this->from('/kontakty')
            ->post('/kontakty', ['name' => '', 'message' => '', 'email' => 'не-пошта'])
            ->assertRedirect('/kontakty');

        $this->followingRedirects()
            ->from('/kontakty')
            ->post('/kontakty', ['name' => 'Відвідувач', 'message' => '', 'email' => 'не-пошта'])
            ->assertOk()
            ->assertSee('форму не надіслано', false)
            ->assertSee('ring-rose-400', false)
            // Введене імʼя не губиться
            ->assertSee('value="Відвідувач"', false);
    }

    public function test_successful_submission_shows_confirmation(): void
    {
        $this->followingRedirects()
            ->from('/kontakty')
            ->post('/kontakty', ['name' => 'Відвідувач', 'message' => 'Питання щодо вступу'])
            ->assertOk()
            ->assertSee('успішно надіслано', false)
            ->assertSee('bg-emerald-50', false);

        $this->assertDatabaseHas('feedback_messages', ['name' => 'Відвідувач']);
    }

    public function test_socials_block_uses_settings(): void
    {
        $this->setContacts([
            'social_facebook' => 'https://facebook.com/otfk',
            'social_youtube' => 'https://youtube.com/@otfk',
        ]);

        $this->get('/kontakty')
            ->assertOk()
            ->assertSee('Ми в соцмережах')
            ->assertSee('https://facebook.com/otfk', false)
            ->assertSee('https://youtube.com/@otfk', false);
    }
}
