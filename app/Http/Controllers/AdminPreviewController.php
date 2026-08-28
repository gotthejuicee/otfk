<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Support\AdminPreview;
use Illuminate\Support\Str;

/**
 * Превʼю несохранённой форми з адмінки: рендерить публічний шаблон
 * сторінки/новини з даними слепка (App\Support\AdminPreview), без запису
 * в БД. Доступ — лише залогіненим адмінам, інакше 404 (як і чернетки).
 */
class AdminPreviewController extends Controller
{
    public function show(string $token)
    {
        abort_unless(auth()->check(), 404);

        $snapshot = AdminPreview::get($token);
        abort_unless($snapshot !== null, 404);

        return match ($snapshot['type']) {
            'page' => $this->page($snapshot['attributes']),
            'news' => $this->news($snapshot['attributes']),
            default => abort(404),
        };
    }

    /** @param array<string, mixed> $attributes */
    private function page(array $attributes)
    {
        $page = new Page;
        $page->forceFill($attributes);

        // Нова сторінка без id: інакше children()/siblings шукали б parent_id = NULL
        // і превʼю помилково рендерилось би хабом з усіма кореневими сторінками.
        $page->id = $page->id ?? 0;
        $page->slug = $page->slug ?: (Str::slug((string) $page->title) ?: 'preview');

        return view('pages.show', ['page' => $page, 'adminPreview' => true]);
    }

    /** @param array<string, mixed> $attributes */
    private function news(array $attributes)
    {
        $news = new News;
        $news->forceFill($attributes);
        $news->id = $news->id ?? 0;
        $news->slug = $news->slug ?: (Str::slug((string) $news->title) ?: 'preview');

        return view('news.show', [
            'news' => $news,
            'related' => News::published()->recent()->whereKeyNot($news->id)->limit(5)->get(),
            'liked' => false,
            'prev' => null,
            'next' => null,
            'abiturientLinks' => collect(),
            'adminPreview' => true,
        ]);
    }
}
