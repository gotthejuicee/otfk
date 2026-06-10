<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsAndEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_animated_stats(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Студентів')
            ->assertSee('data-count', escape: false);
    }

    public function test_events_page_renders_empty_state(): void
    {
        $this->get('/podiyi')
            ->assertOk()
            ->assertSee('Запланованих подій поки немає');
    }

    public function test_upcoming_event_appears_on_home_and_events_page(): void
    {
        Event::create([
            'title' => 'День відкритих дверей',
            'description' => 'Запрошуємо майбутніх студентів',
            'location' => 'Актова зала',
            'starts_at' => now()->addDays(7)->setTime(11, 0),
            'is_published' => true,
        ]);

        $this->get('/')->assertSee('Найближчі події')->assertSee('День відкритих дверей');
        $this->get('/podiyi')->assertSee('День відкритих дверей')->assertSee('Актова зала');
    }

    public function test_past_event_listed_in_past_section(): void
    {
        Event::create([
            'title' => 'Минула конференція',
            'starts_at' => now()->subDays(10),
            'is_published' => true,
        ]);

        $this->get('/podiyi')->assertSee('Минулі події')->assertSee('Минула конференція');
        $this->get('/')->assertDontSee('Минула конференція');
    }
}
