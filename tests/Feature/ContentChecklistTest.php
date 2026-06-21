<?php

namespace Tests\Feature;

use App\Filament\Pages\ContentChecklist;
use App\Filament\Resources\PageResource;
use App\Models\ChecklistDismissal;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ContentChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_hidden_item_drops_out_of_active_count(): void
    {
        $stub = Page::create([
            'title' => 'Заглушка для приховування',
            'slug' => 'zaglushka-hide-test',
            'body' => '<p>коротко</p>',
            'is_published' => true,
        ]);
        $key = 'page:' . $stub->getKey();

        Cache::forget('content_checklist_badge');
        $before = (int) ContentChecklist::getNavigationBadge();

        // Ховаємо пункт вручну — він має зникнути з активного лічильника.
        ChecklistDismissal::create(['item_key' => $key]);
        Cache::forget('content_checklist_badge');
        $after = (int) ContentChecklist::getNavigationBadge();

        $this->assertSame($before - 1, $after);
        $this->assertContains($key, (new ContentChecklist())->dismissedKeys());
    }

    public function test_fill_links_resolve_not_404(): void
    {
        $admin = User::firstOrFail();
        $stub = Page::create([
            'title' => 'Тестова заглушка',
            'slug' => 'testova-zaglushka-chk',
            'body' => '<p>дуже коротко</p>',
            'is_published' => true,
        ]);

        // Filament біндить ці моделі по slug — посилання має бути slug-based, а не по id.
        $editUrl = PageResource::getUrl('edit', ['record' => $stub]);

        $this->actingAs($admin)->get('/admin/content-checklist')
            ->assertOk()
            ->assertSee($editUrl, escape: false);

        // І саме воно відкривається (раніше id-URL давав 404).
        $this->actingAs($admin)->get(parse_url($editUrl, PHP_URL_PATH))->assertOk();
    }

    public function test_checklist_page_renders_for_admin(): void
    {
        $admin = User::firstOrFail(); // створюється сидером

        // Рендер сторінки проганяє всі п'ять запитів-перевірок — ловить будь-яку помилку в них.
        $this->actingAs($admin)->get('/admin/content-checklist')
            ->assertOk()
            ->assertSee('Що ще наповнити')
            ->assertSee('Сторінки без змісту');
    }

    public function test_checklist_requires_login(): void
    {
        $this->get('/admin/content-checklist')->assertRedirect();
    }
}
