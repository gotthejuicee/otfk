<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $upcoming = Event::published()->upcoming()->get();
        $past = Event::published()->past()->limit(6)->get();

        return view('events.index', compact('upcoming', 'past'));
    }
}
