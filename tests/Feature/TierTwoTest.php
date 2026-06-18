<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TierTwoTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_rss_feed_returns_valid_xml(): void
    {
        News::create([
            'title' => 'Тестова новина RSS',
            'slug' => 'testova-novyna-rss',
            'body' => '<p>Текст</p>',
            'excerpt' => 'Короткий опис',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->get(route('news.feed'))
            ->assertOk()
            ->assertHeader('content-type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<rss version="2.0"', escape: false)
            ->assertSee('Тестова новина RSS')
            ->assertSee(route('news.show', News::first()), escape: false);
    }

    public function test_news_show_has_breadcrumb_json_ld(): void
    {
        $news = News::create([
            'title' => 'Новина з крихтами',
            'slug' => 'novyna-z-krykhtamy',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('"@type":"BreadcrumbList"', escape: false)
            ->assertSee('Новина з крихтами')
            ->assertSee('aria-current="page"', escape: false);
    }

    public function test_home_page_has_rss_link_in_head(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('type="application/rss+xml"', escape: false)
            ->assertSee(route('news.feed'), escape: false);
    }
}