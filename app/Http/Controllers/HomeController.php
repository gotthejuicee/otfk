<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\News;
use App\Models\QuickLink;
use App\Models\Video;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->ordered()->get();
        $tiles = QuickLink::visible()->location('home_tile')->ordered()->get();
        $news = News::published()->recent()->with('category')->limit(6)->get();
        $videos = Video::published()->ordered()->limit(6)->get();

        return view('home', compact('banners', 'tiles', 'news', 'videos'));
    }
}
