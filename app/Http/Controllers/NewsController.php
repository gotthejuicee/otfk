<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $categories = NewsCategory::orderBy('sort_order')->get();

        $query = News::published()->recent()->with('category');

        $activeCategory = null;
        if ($slug = $request->query('category')) {
            $activeCategory = NewsCategory::where('slug', $slug)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $news = $query->paginate(9)->withQueryString();

        return view('news.index', compact('news', 'categories', 'activeCategory'));
    }

    public function show(News $news)
    {
        abort_unless($news->is_published, 404);

        $news->increment('views');

        $related = News::published()->recent()
            ->whereKeyNot($news->id)
            ->limit(3)
            ->get();

        return view('news.show', compact('news', 'related'));
    }
}
