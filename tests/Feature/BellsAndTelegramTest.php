<?php

namespace Tests\Feature;

use App\Jobs\PostNewsToTelegram;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BellsAndTelegramTest extends TestCase
{
    use RefreshDatabase;

    public function test_bells_page_renders_with_periods(): void
    {
        $this->get('/rozklad-dzvinkiv')
            ->assertOk()
            ->assertSee('Розклад дзвінків')
            ->assertSee('1-ша пара')
            ->assertSee('Велика перерва');
    }

    public function test_studentu_section_has_bells_tile(): void
    {
        $this->get('/studentu')
            ->assertOk()
            ->assertSee('Розклад дзвінків');
    }

    private function enableAutopost(): void
    {
        Setting::where('key', 'telegram_autopost')->update(['value' => '1']);
        Setting::where('key', 'telegram_bot_token')->update(['value' => 'test-token']);
        Setting::where('key', 'telegram_channel')->update(['value' => '@test_channel']);
        cache()->forget('settings.map');
    }

    public function test_news_dispatches_telegram_post_once(): void
    {
        Bus::fake();
        $this->enableAutopost();

        $news = News::create([
            'title' => 'Новина для Telegram',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subMinute(),
            'is_published' => true,
        ]);

        // Пост летить після віддачі відповіді — адмін не чекає на Telegram.
        Bus::assertDispatchedAfterResponseTimes(PostNewsToTelegram::class, 1);
        $this->assertNotNull($news->fresh()->telegram_posted_at);

        // Повторне збереження не постить вдруге (атомарна позначка).
        $news->fresh()->update(['title' => 'Оновлена назва']);
        Bus::assertDispatchedAfterResponseTimes(PostNewsToTelegram::class, 1);
    }

    public function test_no_autopost_when_disabled(): void
    {
        Bus::fake();

        News::create([
            'title' => 'Тиха новина',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subMinute(),
            'is_published' => true,
        ]);

        Bus::assertNotDispatchedAfterResponse(PostNewsToTelegram::class);
    }

    public function test_job_sends_request_to_telegram(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        $this->enableAutopost();

        $news = News::withoutEvents(fn () => News::create([
            'title' => 'Новина для Telegram',
            'slug' => 'novyna-dlya-telegram',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subMinute(),
            'is_published' => true,
        ]));

        (new PostNewsToTelegram($news))->handle();

        Http::assertSentCount(1);
    }
}
