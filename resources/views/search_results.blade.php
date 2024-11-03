<!-- resources/views/documents/search_results.blade.php -->

<h1>Search Results</h1>

@if($documents->isEmpty())
    <p>No documents found.</p>
@else
    <ul>
        @foreach($documents as $document)
            <li>{{ $document->title }}</li>
        @endforeach
    </ul>
@endif
