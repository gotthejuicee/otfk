<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use App\Models\Specialty;

class QuizController extends Controller
{
    public function index()
    {
        $questions = QuizQuestion::active()
            ->with('options:id,quiz_question_id,label,specialty_id,points,sort_order')
            ->get();

        $specialties = Specialty::published()->ordered()
            ->get(['id', 'title', 'code', 'slug', 'short_description']);

        return view('quiz.index', compact('questions', 'specialties'));
    }
}
