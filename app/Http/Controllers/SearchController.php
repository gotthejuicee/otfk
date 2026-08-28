<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Event;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /** Скільки результатів показуємо на сторінці. */
    private const PER_PAGE = 12;

    /** Типи результатів: ключ фільтра => [назва, назва в множині, іконка]. */
    private const GROUPS = [
        'news' => ['Новина', 'Новини', 'newspaper'],
        'pages' => ['Сторінка', 'Сторінки', 'document-text'],
        'specialties' => ['Спеціальність', 'Спеціальності', 'academic-cap'],
        'documents' => ['Документ', 'Документи', 'folder'],
        'events' => ['Подія', 'Події', 'calendar-days'],
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');

        if (! isset(self::GROUPS[$type])) {
            $type = '';
        }

        $all = mb_strlen($q) >= 2 ? $this->collectResults($q) : new Collection();

        // Лічильники по типах — для чипів-фільтрів (рахуємо до фільтрації)
        $counts = $all->countBy('group');

        $filtered = $type === '' ? $all : $all->where('group', $type)->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $results = new LengthAwarePaginator(
            $filtered->forPage($page, self::PER_PAGE)->values(),
            $filtered->count(),
            self::PER_PAGE,
            $page,
            ['path' => route('search'), 'query' => $request->query()],
        );

        return view('search.index', [
            'q' => $q,
            'type' => $type,
            'results' => $results,
            'counts' => $counts,
            'total' => $all->count(),
            'groups' => self::GROUPS,
            'quickLinks' => $this->quickLinks(),
        ]);
    }

    /** Миттєві підказки для пошуку в шапці (JSON, до 9 результатів). */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $all = $this->collectResults($q);

        // По кілька результатів кожного типу, щоб дропдаун не займала одна секція
        $limits = ['news' => 3, 'pages' => 3, 'specialties' => 2, 'documents' => 2, 'events' => 2];

        $results = collect($limits)
            ->flatMap(fn (int $limit, string $group) => $all->where('group', $group)->take($limit)->values())
            ->map(fn (array $r) => ['group' => $r['label'], 'title' => $r['title'], 'url' => $r['url']])
            ->take(9)
            ->values();

        return response()->json(['results' => $results, 'total' => $results->count()]);
    }

    /**
     * Усі збіги за запитом, згруповані за типом.
     *
     * Фільтруємо колекцію в PHP через mb_stripos, а не через `where('title','like',…)`:
     * у SQLite (dev/тести) LIKE регістронезалежний лише для ASCII, тож «положення»
     * не знайшло б «Положення …» (Gotcha 21). Вибірки маленькі — тільки потрібні колонки.
     */
    private function collectResults(string $q): Collection
    {
        $matches = fn (Collection $rows, string $field) => $rows
            ->filter(fn ($row) => mb_stripos((string) $row->{$field}, $q) !== false)
            // Спершу ті, де запит на початку назви
            ->sortBy(fn ($row) => mb_stripos((string) $row->{$field}, $q) === 0 ? 0 : 1)
            ->values();

        $news = $matches(
            News::published()->recent()->get(['id', 'slug', 'title', 'excerpt', 'published_at']),
            'title'
        )->map(fn (News $n) => $this->item('news', $n->title, route('news.show', $n), $n->excerpt, $n->published_at?->translatedFormat('j F Y')));

        $specialties = $matches(
            Specialty::published()->ordered()->get(['id', 'slug', 'title', 'code', 'short_description']),
            'title'
        )->map(fn (Specialty $s) => $this->item('specialties', $s->title, route('specialties.show', $s), $s->short_description, $s->code));

        $pages = $matches(
            Page::published()->with('parent:id,title')->get(['id', 'slug', 'title', 'excerpt', 'parent_id']),
            'title'
        )->map(fn (Page $p) => $this->item('pages', $p->title, url('/' . $p->slug), $p->excerpt, $p->parent?->title));

        $documents = $matches(
            Document::published()->with('category:id,slug,title')->get(['id', 'document_category_id', 'title', 'description', 'file_path', 'external_url', 'published_at']),
            'title'
        )->map(fn (Document $d) => $this->item(
            'documents',
            $d->title,
            $d->file_url ?: route('documents.index'),
            $d->description,
            $d->category?->title,
            (bool) $d->file_url,
        ));

        $events = $matches(
            Event::published()->orderByDesc('starts_at')->get(['id', 'title', 'description', 'location', 'starts_at']),
            'title'
        )->map(fn (Event $e) => $this->item('events', $e->title, route('events'), $e->description, $e->starts_at?->translatedFormat('j F Y')));

        return $news->concat($pages)->concat($specialties)->concat($documents)->concat($events)->values();
    }

    /** Один результат пошуку у форматі, який очікує шаблон. */
    private function item(string $group, string $title, string $url, ?string $excerpt, ?string $meta = null, bool $external = false): array
    {
        return [
            'group' => $group,
            'label' => self::GROUPS[$group][0],
            'icon' => self::GROUPS[$group][2],
            'title' => $title,
            'url' => $url,
            'excerpt' => $excerpt ? \Illuminate\Support\Str::limit(strip_tags($excerpt), 180) : null,
            'meta' => $meta,
            'external' => $external,
        ];
    }

    /**
     * Швидкі посилання для порожнього запиту та стану «нічого не знайдено» —
     * верхній рівень меню з адмінки, без хардкоду розділів у шаблоні.
     */
    private function quickLinks(): Collection
    {
        return MenuItem::navigation()
            ->map(fn (MenuItem $item) => ['label' => $item->label, 'url' => $item->href])
            ->filter(fn (array $link) => $link['url'] !== '#')
            ->take(8)
            ->values();
    }
}
