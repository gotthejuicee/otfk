<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;

class DocumentController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::ordered()
            ->withCount(['documents' => fn ($q) => $q->published()])
            ->get();

        return view('documents.index', compact('categories'));
    }

    public function category(DocumentCategory $documentCategory)
    {
        $documents = $documentCategory->documents()->published()->get();
        $categories = DocumentCategory::ordered()->get();

        return view('documents.category', [
            'category' => $documentCategory,
            'documents' => $documents,
            'categories' => $categories,
        ]);
    }
}
