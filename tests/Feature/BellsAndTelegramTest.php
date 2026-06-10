<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('1-ша пара');
    }

    public function test_news_autoposts_to_telegram_once(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        Setting::where('key', 'telegram_autopost')->update(['value' => '1']);
        Setting::where('key', 'telegram_bot_token')->update(['value' => 'test-token']);
        Setting::where('key', 'telegram_channel')->update(['value' => '@test_channel']);
        cache()->forget('settings.map');

        $news = News::create([
            'title' => 'Новина для Telegram',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subMinute(),
            'is_published' => true,
        ]);

        Http::assertSentCount(1);
        $this->assertNotNull($news->fresh()->telegram_posted_at);

        // Повторне збереження не постить вдруге
        $news->fresh()->update(['title' => 'Оновлена назва']);
        Http::assertSentCount(1);
    }

    public function test_no_autopost_when_disabled(): void
    {
        Http::fake();

        News::create([
            'title' => 'Тиха новина',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subMinute(),
            'is_published' => true,
        ]);

        Http::assertNothingSent();
    }
}
