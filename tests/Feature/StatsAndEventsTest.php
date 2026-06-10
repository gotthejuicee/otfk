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

    public function test_event_ics_download_and_calendar_buttons(): void
    {
        $event = Event::create([
            'title' => 'День відкритих дверей; запрошуємо',
            'description' => 'Знайомство, екскурсія',
            'location' => 'Актова зала',
            'starts_at' => now()->addDays(5)->setTime(11, 0),
            'is_published' => true,
        ]);

        // Кнопки на сторінці подій
        $this->get('/podiyi')
            ->assertSee('Google Календар')
            ->assertSee('calendar.google.com/calendar/render', escape: false)
            ->assertSee(route('events.ics', $event), escape: false);

        // Файл .ics: заголовки + структура + екранування «;»
        $resp = $this->get(route('events.ics', $event));
        $resp->assertOk()->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $body = $resp->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $body);
        $this->assertStringContainsString('SUMMARY:День відкритих дверей\; запрошуємо', $body);
        $this->assertStringContainsString('LOCATION:Актова зала', $body);
        $this->assertStringContainsString('DTSTART:', $body);

        // Неопублікована подія — 404
        $event->update(['is_published' => false]);
        $this->get(route('events.ics', $event))->assertNotFound();
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
