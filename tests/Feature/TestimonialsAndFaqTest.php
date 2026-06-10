<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialsAndFaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_renders_with_rich_results_markup(): void
    {
        $this->get('/faq')
            ->assertOk()
            ->assertSee('Питання та відповіді')
            ->assertSee('Як подати заявку на вступ онлайн?')
            ->assertSee('"@type":"FAQPage"', escape: false);
    }

    public function test_abituriyentu_section_has_faq_tile(): void
    {
        $this->get('/abituriyentu')->assertOk()->assertSee('Питання та відповіді');
    }

    public function test_home_hides_testimonials_when_empty(): void
    {
        $this->get('/')->assertDontSee('Відгуки студентів та випускників');
    }

    public function test_home_shows_testimonials_when_present(): void
    {
        Testimonial::create([
            'name' => 'Олена Коваль',
            'role' => 'Випускниця 2024',
            'quote' => 'Коледж дав мені і знання, і друзів на все життя.',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertSee('Відгуки студентів та випускників')
            ->assertSee('Олена Коваль')
            ->assertSee('ОК'); // ініціали-аватар без фото
    }
}
