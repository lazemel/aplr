<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
        // Validate the search query
        $request->validate([
            'query' => 'required|string',
        ]);

        // Perform the search
        $documents = Document::search($request->input('query'))->get();

        // Return the search results
        return view('documents.search_results', compact('documents'));
    }
}
