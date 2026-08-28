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

    public function show(Staff $staff)
    {
        abort_unless($staff->is_published, 404);

        $staff->load('department');

        $colleagues = $staff->department_id
            ? Staff::published()->where('department_id', $staff->department_id)->whereKeyNot($staff->id)->ordered()->take(8)->get()
            : collect();

        return view('staff.show', compact('staff', 'colleagues'));
    }
}
