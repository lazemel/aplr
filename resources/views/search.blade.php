<!DOCTYPE html>
<html>
<head>
    <title>Search Documents</title>
</head>
<body>
    <h1>Search Documents</h1>

    <form action="{{ route('documents.search') }}" method="GET">
        <input type="text" name="query" placeholder="Search documents..." required>
        <button type="submit">Search</button>
    </form>

    @if(isset($documents))
        <h2>Results:</h2>
        @if($documents->isEmpty())
            <p>No documents found.</p>
        @else
            <ul>
                @foreach($documents as $document)
                    <li>{{ $document->title }}</li>
                @endforeach
            </ul>
        @endif
    @endif
</body>
</html>
