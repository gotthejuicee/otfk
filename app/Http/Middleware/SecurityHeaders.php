<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Базові захисні заголовки для всіх відповідей (публічна частина + адмінка).
     * CSP свідомо не додаємо: Livewire/Alpine/Filament покладаються на інлайн-скрипти,
     * і строгий CSP їх зламає.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // HSTS — лише по HTTPS, 180 днів (без preload: домен тестовий, лишаємо шлях назад)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=15552000');
        }

        return $response;
    }
}
