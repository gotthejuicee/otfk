<?php

namespace Tests\Feature;

use App\Models\ApplicantRequest;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantAndAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_form_renders(): void
    {
        $this->get('/zayavka')
            ->assertOk()
            ->assertSee('Залишити заявку')
            ->assertSee('Яка спеціальність цікавить?');
    }

    public function test_application_is_stored(): void
    {
        $this->post('/zayavka', [
            'name' => 'Тарас Шевченко',
            'phone' => '+380501234567',
            'email' => 'taras@example.com',
            'message' => 'Чи є бюджетні місця?',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('applicant_requests', [
            'name' => 'Тарас Шевченко',
            'phone' => '+380501234567',
            'is_processed' => false,
        ]);
    }

    public function test_honeypot_blocks_bots_silently(): void
    {
        $this->post('/zayavka', [
            'name' => 'Бот',
            'phone' => '123',
            'website' => 'spam.example',
        ])->assertRedirect();

        $this->assertSame(0, ApplicantRequest::count());
    }

    public function test_announcement_banner_shows_when_set(): void
    {
        $this->get('/')->assertDontSee('id="announcement"', escape: false);

        Setting::where('key', 'announcement_text')->update(['value' => 'Завтра коледж працює дистанційно!']);
        cache()->forget('settings.map');

        $this->get('/')->assertSee('Завтра коледж працює дистанційно!');
    }

    public function test_abituriyentu_section_has_zayavka_tile(): void
    {
        $this->get('/abituriyentu')->assertOk()->assertSee('Залишити заявку');
    }
}
