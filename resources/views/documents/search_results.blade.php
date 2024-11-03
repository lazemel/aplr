<!-- resources/views/documents/search_results.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
</head>
<body>
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

    <a href="{{ url('/') }}">Go back</a>
</body>
</html>
