<?php

namespace App\Http\Controllers;

use App\Models\Document;
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
}
