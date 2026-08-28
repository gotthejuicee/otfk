<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Превʼю чернеток з адмінки: неопубліковані сторінки та новини
 * бачать лише залогінені адміністратори — з плашкою «Чернетка»;
 * для гостей вони, як і раніше, 404. Перегляди чернеток не рахуються.
 */
class DraftPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function draftPage(): Page
    {
        return Page::create([
            'title' => 'Таємна сторінка',
            'slug' => 'tayemna-storinka',
            'body' => '<p>Текст чернетки.</p>',
            'is_published' => false,
        ]);
    }

    private function draftNews(): News
    {
        return News::create([
            'title' => 'Таємна новина',
            'slug' => 'tayemna-novyna',
            'body' => '<p>Текст чернетки.</p>',
            'published_at' => now(),
            'is_published' => false,
        ]);
    }

    public function test_guest_gets_404_for_unpublished_page(): void
    {
        $page = $this->draftPage();

        $this->get('/' . $page->slug)->assertNotFound();
    }

    public function test_admin_sees_unpublished_page_with_draft_notice(): void
    {
        $page = $this->draftPage();

        $this->actingAs($this->admin())
            ->get('/' . $page->slug)
            ->assertOk()
            ->assertSee('Чернетка');
    }

    public function test_published_page_has_no_draft_notice(): void
    {
        $page = $this->draftPage();
        $page->update(['is_published' => true]);

        $this->actingAs($this->admin())
            ->get('/' . $page->slug)
            ->assertOk()
            ->assertDontSee('Чернетка');
    }

    public function test_guest_gets_404_for_unpublished_news(): void
    {
        $news = $this->draftNews();

        $this->get('/novyny/' . $news->slug)->assertNotFound();
    }

    public function test_admin_sees_unpublished_news_without_view_increment(): void
    {
        $news = $this->draftNews();

        $this->actingAs($this->admin())
            ->get('/novyny/' . $news->slug)
            ->assertOk()
            ->assertSee('Чернетка');

        $this->assertSame(0, (int) $news->fresh()->views);
    }
}
