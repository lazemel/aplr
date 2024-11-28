<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $searchQuery = $request->input('query');

        $documents = Document::search()
            ->options([
                'q' => $searchQuery,
                'query_by' => 'title,paragraphs',
                'per_page' => 25,
                'page' => $request->get('page', 1)
            ])
            ->paginate(25);

        return view('documents.search_results', compact('documents'));
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }
}

