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

        $documents = Document::search($request->input('query'))->get();

        return view('documents.search_results', compact('documents'));
    }
}

