<?php

namespace App\Http\Controllers;

use App\Models\Staff;

class StaffController extends Controller
{
    public function administration()
    {
        $staff = Staff::published()->administration()->ordered()->get();

        return view('staff.administration', compact('staff'));
    }
}
