<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Event;
use App\Models\News;
use App\Models\QuickLink;
use App\Models\StatItem;
use App\Models\Video;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->ordered()->get();
        $tiles = QuickLink::visible()->location('home_tile')->ordered()->get();
        $stats = StatItem::active()->get();
        $events = Event::published()->upcoming()->limit(3)->get();
        $news = News::published()->recent()->with('category')->limit(6)->get();
        $videos = Video::published()->ordered()->limit(6)->get();

        return view('home', compact('banners', 'tiles', 'stats', 'events', 'news', 'videos'));
    }
}
