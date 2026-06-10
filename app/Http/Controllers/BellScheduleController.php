<?php

namespace App\Http\Controllers;

use App\Models\BellPeriod;

class BellScheduleController extends Controller
{
    public function index()
    {
        $periods = BellPeriod::active();

        return view('bells.index', compact('periods'));
    }
}
