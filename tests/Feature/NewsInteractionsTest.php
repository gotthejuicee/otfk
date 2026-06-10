<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsInteractionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeNews(): News
    {
        return News::create([
            'title' => 'Тестова новина',
            'slug' => 'testova-novyna',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);
    }

    public function test_like_toggles_without_registration(): void
    {
        $news = $this->makeNews();

        $this->postJson(route('news.like', $news))
            ->assertOk()
            ->assertJson(['likes' => 1, 'liked' => true]);

        $this->postJson(route('news.like', $news))
            ->assertOk()
            ->assertJson(['likes' => 0, 'liked' => false]);
    }

    public function test_views_increment_once_per_session(): void
    {
        $news = $this->makeNews();

        $this->get(route('news.show', $news))->assertOk();
        $this->get(route('news.show', $news))->assertOk();

        $this->assertSame(1, $news->fresh()->views);
    }

    public function test_unpublished_news_cannot_be_liked(): void
    {
        $news = $this->makeNews();
        $news->update(['is_published' => false]);

        $this->postJson(route('news.like', $news))->assertNotFound();
    }
}
