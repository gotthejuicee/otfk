<?php

namespace App\Http\Controllers;

use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::published()->ordered()->paginate(12);

        return view('videos.index', compact('videos'));
    }
}
