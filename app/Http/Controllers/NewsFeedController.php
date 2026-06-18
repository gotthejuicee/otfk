<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Setting;

class NewsFeedController extends Controller
{
    public function __invoke()
    {
        $news = News::published()->recent()->limit(30)->get();
        $siteName = config('app.name');
        $description = Setting::get('site_description')
            ?? 'Новини Одеського технічного фахового коледжу ОНТУ';

        return response()
            ->view('feed.news', compact('news', 'siteName', 'description'), 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}