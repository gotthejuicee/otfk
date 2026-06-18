<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeritageProseTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_news_uses_warm_prose_without_heritage_frame(): void
    {
        $news = News::create([
            'title' => 'Звичайна новина',
            'slug' => 'zvychayna-novyna',
            'body' => '<p>Перший абзац тестової новини.</p>',
            'is_published' => true,
            'published_at' => now(),
            'is_heritage' => false,
        ]);

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('prose-site', escape: false)
            ->assertDontSee('heritage-frame', escape: false)
            ->assertDontSee('prose-heritage', escape: false);
    }

    public function test_heritage_news_uses_letter_style(): void
    {
        $news = News::create([
            'title' => 'Ювілей коледжу',
            'slug' => 'yuviley-koledzhu',
            'body' => '<p>Урочистий текст з архіву.</p>',
            'is_published' => true,
            'published_at' => now()->setDate(2020, 3, 15),
            'is_heritage' => true,
        ]);

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('heritage-frame', escape: false)
            ->assertSee('prose-heritage', escape: false)
            ->assertSee('Особлива публікація')
            ->assertSee('З повагою', escape: false)
            ->assertSee('Одеса · 15 березня 2020');
    }

    public function test_heritage_page_uses_letter_style(): void
    {
        $page = Page::create([
            'title' => 'Історія коледжу',
            'slug' => 'istoriya-koledzhu',
            'body' => '<p>Хроніка заснування.</p>',
            'is_published' => true,
            'is_heritage' => true,
        ]);

        $this->get('/' . $page->slug)
            ->assertOk()
            ->assertSee('heritage-frame', escape: false)
            ->assertSee('prose-heritage', escape: false);
    }
}