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

    public function show(Request $request, News $news)
    {
        abort_unless($news->is_published, 404);

        // Чесний лічильник: +1 лише раз за сесію відвідувача (не накручується F5).
        if (! $request->session()->has("viewed_news.{$news->id}")) {
            $news->increment('views');
            $request->session()->put("viewed_news.{$news->id}", true);
        }

        $liked = $news->likeRecords()
            ->where('fingerprint', $this->fingerprint($request))
            ->exists();

        $related = News::published()->recent()
            ->whereKeyNot($news->id)
            ->limit(3)
            ->get();

        return view('news.show', compact('news', 'related', 'liked'));
    }

    /** Вподобайка без реєстрації: один лайк на відвідувача, повторний клік знімає. */
    public function like(Request $request, News $news)
    {
        abort_unless($news->is_published, 404);

        $fp = $this->fingerprint($request);

        $existing = $news->likeRecords()->where('fingerprint', $fp)->first();

        if ($existing) {
            $existing->delete();
            $news->where('id', $news->id)->where('likes', '>', 0)->decrement('likes');
            $liked = false;
        } else {
            $news->likeRecords()->create(['fingerprint' => $fp]);
            $news->increment('likes');
            $liked = true;
        }

        return response()->json([
            'likes' => (int) $news->fresh()->likes,
            'liked' => $liked,
        ]);
    }

    /** Анонімний відбиток відвідувача (IP + браузер) — без реєстрації та кук. */
    private function fingerprint(Request $request): string
    {
        return sha1($request->ip() . '|' . (string) $request->userAgent());
    }
}
