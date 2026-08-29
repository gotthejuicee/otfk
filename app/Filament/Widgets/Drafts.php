<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\NewsResource;
use App\Filament\Resources\PageResource;
use App\Models\News;
use App\Models\Page;
use Filament\Widgets\Widget;

/**
 * Чернетки сторінок і новин одним списком на дашборді: редагування + перегляд
 * на сайті (залогінений адмін бачить чернетку з жовтою плашкою — DraftPreviewTest).
 * Коли чернеток немає, віджет ховається цілком.
 */
class Drafts extends Widget
{
    protected static string $view = 'filament.widgets.drafts';

    /** Список маленький і важливий — рендеримо одразу, без відкладеного Livewire. */
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Page::query()->where('is_published', false)->exists()
            || News::query()->where('is_published', false)->exists();
    }

    /** @return list<array{type: string, title: string, edit_url: string, view_url: string, updated: \Illuminate\Support\Carbon|null}> */
    public function drafts(): array
    {
        $pages = Page::query()->where('is_published', false)
            ->orderByDesc('updated_at')->limit(10)->get()
            ->map(fn (Page $page) => [
                'type' => 'Сторінка',
                'title' => $page->title,
                'edit_url' => PageResource::getUrl('edit', ['record' => $page]),
                'view_url' => url('/' . $page->slug),
                'updated' => $page->updated_at,
            ]);

        $news = News::query()->where('is_published', false)
            ->orderByDesc('updated_at')->limit(10)->get()
            ->map(fn (News $item) => [
                'type' => 'Новина',
                'title' => $item->title,
                'edit_url' => NewsResource::getUrl('edit', ['record' => $item]),
                'view_url' => route('news.show', $item),
                'updated' => $item->updated_at,
            ]);

        return $pages->concat($news)->sortByDesc('updated')->take(10)->values()->all();
    }
}
