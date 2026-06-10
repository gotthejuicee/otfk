<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $results = new Collection();

        if (mb_strlen($q) >= 2) {
            $like = '%' . $q . '%';

            $results = $results
                ->concat(News::published()->where('title', 'like', $like)->limit(15)->get()
                    ->map(fn ($n) => ['type' => 'Новина', 'title' => $n->title, 'url' => route('news.show', $n), 'excerpt' => $n->excerpt]))
                ->concat(Specialty::published()->where('title', 'like', $like)->limit(15)->get()
                    ->map(fn ($s) => ['type' => 'Спеціальність', 'title' => $s->title, 'url' => route('specialties.show', $s), 'excerpt' => $s->short_description]))
                ->concat(Page::published()->where('title', 'like', $like)->limit(15)->get()
                    ->map(fn ($p) => ['type' => 'Сторінка', 'title' => $p->title, 'url' => url('/' . $p->slug), 'excerpt' => $p->excerpt]))
                ->concat(Document::published()->where('title', 'like', $like)->limit(15)->get()
                    ->map(fn ($d) => ['type' => 'Документ', 'title' => $d->title, 'url' => $d->file_url ?: route('documents.index'), 'excerpt' => $d->description]));
        }

        return view('search.index', compact('q', 'results'));
    }

    /** Миттєві підказки для пошуку в шапці (JSON, до 9 результатів). */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $like = '%' . $q . '%';

        $results = collect()
            ->concat(News::published()->where('title', 'like', $like)->recent()->limit(3)->get()
                ->map(fn ($n) => ['group' => 'Новина', 'title' => $n->title, 'url' => route('news.show', $n)]))
            ->concat(Page::published()->where('title', 'like', $like)->limit(3)->get()
                ->map(fn ($p) => ['group' => 'Сторінка', 'title' => $p->title, 'url' => url('/' . $p->slug)]))
            ->concat(Specialty::published()->where('title', 'like', $like)->limit(2)->get()
                ->map(fn ($s) => ['group' => 'Спеціальність', 'title' => $s->title, 'url' => route('specialties.show', $s)]))
            ->concat(Document::published()->where('title', 'like', $like)->limit(2)->get()
                ->map(fn ($d) => ['group' => 'Документ', 'title' => $d->title, 'url' => $d->file_url ?: route('documents.index')]))
            ->concat(Event::published()->upcoming()->where('title', 'like', $like)->limit(2)->get()
                ->map(fn ($e) => ['group' => 'Подія', 'title' => $e->title . ' (' . $e->starts_at->format('d.m') . ')', 'url' => route('events')]))
            ->take(9)
            ->values();

        return response()->json(['results' => $results, 'total' => $results->count()]);
    }
}
