<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_skip_link_present_on_home(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Перейти до основного вмісту')
            ->assertSee('id="main-content"', escape: false);
    }

    public function test_home_uses_plain_summary_card(): void
    {
        // Без обкладинки сторінки — звичайна (не велика) Twitter-картка.
        $this->get('/')
            ->assertOk()
            ->assertSee('name="twitter:card" content="summary"', escape: false);
    }

    public function test_news_with_cover_uses_large_social_card(): void
    {
        $news = News::create([
            'title' => 'Новина з обкладинкою',
            'slug' => 'novyna-z-obkladynkoyu',
            'body' => '<p>Текст</p>',
            'cover_image' => 'news/cover.jpg',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('summary_large_image')
            ->assertSee('storage/news/cover.jpg', escape: false);
    }

    public function test_sitemap_has_priority_changefreq_and_lastmod(): void
    {
        News::create([
            'title' => 'Новина для мапи',
            'slug' => 'novyna-dlya-mapy',
            'body' => '<p>Текст</p>',
            'published_at' => now()->subDay(),
            'is_published' => true,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<priority>', escape: false)
            ->assertSee('<changefreq>', escape: false)
            ->assertSee('<lastmod>', escape: false);
    }
}
