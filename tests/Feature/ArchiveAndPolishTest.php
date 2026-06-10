<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveAndPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_year_filter(): void
    {
        News::create(['title' => 'Стара новина', 'body' => '<p>т</p>', 'published_at' => now()->subYears(3), 'is_published' => true]);
        News::create(['title' => 'Свіжа новина', 'body' => '<p>т</p>', 'published_at' => now()->subDay(), 'is_published' => true]);

        $oldYear = now()->subYears(3)->year;

        $this->get('/novyny?year=' . $oldYear)
            ->assertOk()
            ->assertSee('Стара новина')
            ->assertDontSee('Свіжа новина');
    }

    public function test_on_this_day_block_appears_on_home(): void
    {
        News::create([
            'title' => 'Історична подія коледжу',
            'body' => '<p>т</p>',
            'published_at' => now()->subYears(2),
            'is_published' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Цього дня у ' . now()->subYears(2)->year . ' році')
            ->assertSee('Історична подія коледжу');
    }

    public function test_home_without_archive_hides_block(): void
    {
        // Свіжі новини без минулих років — блок прихований
        News::create(['title' => 'Нова', 'body' => '<p>т</p>', 'published_at' => now()->subDay(), 'is_published' => true]);

        $this->get('/')->assertOk()->assertDontSee('Цього дня у');
    }

    public function test_admin_dashboard_renders_quick_actions(): void
    {
        $admin = \App\Models\User::firstOrFail(); // створюється сидером

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Швидкі дії')
            ->assertSee('Додати новину');
    }

    public function test_404_page_has_live_search(): void
    {
        $this->get('/takoyi-storinky-tochno-nemaye')
            ->assertNotFound()
            ->assertSee('Що ви шукали?')
            ->assertSee('Розклад дзвінків');
    }
}
