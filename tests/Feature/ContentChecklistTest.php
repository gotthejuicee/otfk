<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentChecklistTest extends TestCase
{
    use RefreshDatabase;

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
