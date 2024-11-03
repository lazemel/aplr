<!-- resources/views/documents/search.blade.php -->

<form action="{{ route('documents.search') }}" method="GET">
    <input type="text" name="query" placeholder="Search documents..." required>
    <button type="submit">Search</button>
</form>
