<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Смоук-тест публічної частини: кожен розділ має відповідати 200,
 * невідомий слаг — 404, захисні заголовки — присутні.
 * Працює на sqlite у памʼяті (МySQL не потрібен): міграції самі наповнюють
 * меню/сторінки, сидер додає демо-контент і налаштування.
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function publicRoutes(): array
    {
        return [
            'головна' => ['/'],
            'новини' => ['/novyny'],
            'відео' => ['/video'],
            'документи' => ['/dokumenty'],
            'спеціальності' => ['/spetsialnosti'],
            'структура' => ['/struktura'],
            'адміністрація' => ['/administratsiya'],
            'галерея' => ['/halereya'],
            'пошук' => ['/poshuk?q=коледж'],
            'контакти' => ['/kontakty'],
            'сторінка через catch-all' => ['/abituriyentu'],
            'sitemap' => ['/sitemap.xml'],
            'robots' => ['/robots.txt'],
        ];
    }

    #[DataProvider('publicRoutes')]
    public function test_public_page_responds_ok(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/takoyi-storinky-nemaye')->assertNotFound();
    }

    public function test_admin_redirects_guests_to_login(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_security_headers_present(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
