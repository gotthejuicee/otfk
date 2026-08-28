<?php

namespace App\Http\Controllers;

use App\Models\Department;

class StructureController extends Controller
{
    public function index()
    {
        $groups = [];
        foreach (Department::TYPES as $type => $label) {
            $items = Department::published()->where('type', $type)->ordered()->withCount('staff')->get();
            if ($items->isNotEmpty()) {
                $groups[$type] = ['label' => $label, 'items' => $items];
            }
        }

        return view('structure.index', compact('groups'));
    }

    public function show(Department $department)
    {
        abort_unless($department->is_published, 404);

        $department->load(['staff' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')]);

        // Блок «Інші підрозділи» — сусіди тієї ж групи (відділення / комісія / кафедра)
        $others = Department::published()
            ->where('type', $department->type)
            ->whereKeyNot($department->getKey())
            ->ordered()
            ->withCount('staff')
            ->take(4)
            ->get();

        return view('structure.show', compact('department', 'others'));
    }
}
