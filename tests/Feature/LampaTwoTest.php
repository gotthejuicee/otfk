<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LampaTwoTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_inherits_heritage_from_category(): void
    {
        $category = NewsCategory::create([
            'title' => 'Архів',
            'slug' => 'arkhiv',
            'is_heritage' => true,
        ]);

        $news = News::create([
            'category_id' => $category->id,
            'title' => 'Подія з минулого',
            'slug' => 'podiya-z-mynuloho',
            'body' => '<p>Архівний текст.</p>',
            'is_published' => true,
            'published_at' => now()->subYear(),
            'is_heritage' => false,
        ]);

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('heritage-frame', escape: false)
            ->assertSee('Особлива публікація');
    }

    public function test_news_item_heritage_flag_works_without_category(): void
    {
        $news = News::create([
            'title' => 'Ювілей',
            'slug' => 'yuviley',
            'body' => '<p>Урочистий текст.</p>',
            'is_published' => true,
            'published_at' => now(),
            'is_heritage' => true,
        ]);

        $this->assertTrue($news->usesHeritagePresentation());

        $this->get(route('news.show', $news))
            ->assertOk()
            ->assertSee('heritage-frame', escape: false);
    }

    public function test_archive_gallery_uses_archive_styles(): void
    {
        $gallery = Gallery::create([
            'title' => 'Коледж 1990-х',
            'slug' => 'koledzh-1990',
            'is_published' => true,
            'is_archive' => true,
        ]);

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertSee('photo-archive', escape: false)
            ->assertSee('Архівний альбом');
    }

    public function test_standard_gallery_without_archive_styles(): void
    {
        $gallery = Gallery::create([
            'title' => 'Сучасний альбом',
            'slug' => 'suchasnyy-albom',
            'is_published' => true,
            'is_archive' => false,
        ]);

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertDontSee('photo-archive', escape: false)
            ->assertDontSee('Архівний альбом');
    }

    public function test_on_this_day_uses_heritage_teaser_from_category(): void
    {
        $category = NewsCategory::create([
            'title' => 'Історія',
            'slug' => 'istoriya',
            'is_heritage' => true,
        ]);

        News::create([
            'category_id' => $category->id,
            'title' => 'Святкування в коледжі',
            'body' => '<p>т</p>',
            'published_at' => now()->subYears(2),
            'is_published' => true,
            'is_heritage' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('heritage-teaser', escape: false)
            ->assertSee('Святкування в коледжі');
    }
}