<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Page;
use App\Models\User;
use App\Support\AdminPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Превʼю несохранённой форми (кнопка «Превʼю» у формах сторінок/новин):
 * слепок стану форми в кеші → /admin-preview/{token} рендерить публічний
 * шаблон без запису в БД. Лише для залогінених; чужий/протухлий токен — 404.
 */
class FormPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_gets_404_even_with_valid_token(): void
    {
        $token = AdminPreview::store('page', new Page, ['title' => 'Секрет']);

        $this->get('/admin-preview/' . $token)->assertNotFound();
    }

    public function test_unknown_token_returns_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin-preview/nemaye-takogo-tokena')
            ->assertNotFound();
    }

    public function test_new_page_form_state_renders_without_saving(): void
    {
        // Контент-міграції самі наповнюють таблицю — фіксуємо, що превʼю нічого не додає.
        $before = Page::count();

        $token = AdminPreview::store('page', new Page, [
            'title' => 'Ще не збережена сторінка',
            'body' => '<p>Текст з форми.</p>',
            'is_published' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin-preview/' . $token)
            ->assertOk()
            ->assertSee('Ще не збережена сторінка')
            ->assertSee('Текст з форми.')
            ->assertSee('Попередній перегляд');

        $this->assertSame($before, Page::count());
    }

    public function test_edited_record_preview_shows_unsaved_changes(): void
    {
        $page = Page::create([
            'title' => 'Стара назва',
            'slug' => 'stara-nazva',
            'body' => '<p>Старий текст.</p>',
            'is_published' => true,
        ]);

        $token = AdminPreview::store('page', $page, ['title' => 'Нова назва з форми']);

        $this->actingAs(User::factory()->create())
            ->get('/admin-preview/' . $token)
            ->assertOk()
            ->assertSee('Нова назва з форми')
            ->assertSee('Старий текст.');

        $this->assertSame('Стара назва', $page->fresh()->title);
    }

    public function test_news_form_state_renders_without_saving(): void
    {
        $before = News::count();

        $token = AdminPreview::store('news', new News, [
            'title' => 'Ще не збережена новина',
            'body' => '<p>Тіло новини з форми.</p>',
            'published_at' => now()->toDateTimeString(),
            'is_published' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin-preview/' . $token)
            ->assertOk()
            ->assertSee('Ще не збережена новина')
            ->assertSee('Тіло новини з форми.')
            ->assertSee('Попередній перегляд');

        $this->assertSame($before, News::count());
    }

    public function test_non_scalar_form_state_is_ignored(): void
    {
        $token = AdminPreview::store('page', new Page, [
            'title' => 'Сторінка з файлом',
            'cover_image' => ['tmp-upload-object'],
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin-preview/' . $token)
            ->assertOk()
            ->assertSee('Сторінка з файлом');
    }
}
