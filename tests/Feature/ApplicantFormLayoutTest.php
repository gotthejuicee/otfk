<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Редизайн сторінки заявки: світла шапка, двоколонковий блок з сайдбаром, смуга переваг. */
class ApplicantFormLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_has_light_header_and_two_column_layout(): void
    {
        $html = $this->get('/zayavka')->assertOk()->getContent();

        // Світла шапка-картка замість navy-героя (як на новинах, відео та галереї)
        $this->assertStringContainsString('border-b border-slate-200/70 bg-slate-50/80', $html);
        // Заголовок — темний по білому, а не білий по navy
        $this->assertMatchesRegularExpression('/text-brand-950[^>]*>Залишити заявку/u', $html);

        // Форма ліворуч (2 колонки з 3), сайдбар праворуч
        $this->assertStringContainsString('lg:grid-cols-3', $html);
        $this->assertStringContainsString('lg:col-span-2', $html);
    }

    public function test_sidebar_shows_steps_contacts_and_quiz_cta(): void
    {
        $this->get('/zayavka')
            ->assertOk()
            ->assertSee('Що буде далі')
            ->assertSee('Отримуємо вашу заявку')
            ->assertSee('Контакти приймальної комісії')
            ->assertSee('Яка спеціальність мені підходить?')
            ->assertSee(route('quiz'), false);
    }

    public function test_contacts_block_hides_when_settings_are_empty(): void
    {
        Setting::whereIn('key', ['contact_phone', 'contact_email', 'contact_address', 'work_hours'])
            ->update(['value' => '']);
        cache()->forget('settings.map');

        $this->get('/zayavka')->assertOk()->assertDontSee('Контакти приймальної комісії');
    }

    public function test_form_fields_are_touch_friendly_and_typed(): void
    {
        $html = $this->get('/zayavka')->assertOk()->getContent();

        // Крупні поля під палець + правильні клавіатури на мобільному
        $this->assertStringContainsString('inputmode="tel"', $html);
        $this->assertStringContainsString('inputmode="email"', $html);
        $this->assertStringContainsString('autocomplete="name"', $html);
        $this->assertStringContainsString('input px-4 py-3 text-base', $html);
    }

    public function test_validation_errors_render_summary_banner(): void
    {
        $this->from('/zayavka')->post('/zayavka', ['name' => '', 'phone' => ''])
            ->assertRedirect('/zayavka')
            ->assertSessionHasErrors(['name', 'phone']);

        $this->followingRedirects()
            ->from('/zayavka')
            ->post('/zayavka', ['name' => '', 'phone' => ''])
            ->assertOk()
            ->assertSee('Перевірте, будь ласка, підсвічені поля — форму не надіслано.');
    }

    public function test_benefits_strip_is_rendered(): void
    {
        $this->get('/zayavka')
            ->assertOk()
            ->assertSee('Безкоштовна консультація')
            ->assertSee('Підтримка на етапі вступу');
    }
}
