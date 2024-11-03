<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <form action="{{ route('documents.search') }}" method="GET" class="max-w-lg w-full">
        <div class="flex">
            <input type="search" name="query" placeholder="Search Mockups, Logos, Design Templates..." required
                class="block p-4 w-full text-sm text-gray-900 bg-gray-50 border border-gray-300 focus:ring-2 focus:ring-blue-500 placeholder-gray-500" />
            <button type="submit" class="flex-shrink-0 p-4 text-sm font-medium text-white bg-blue-700 border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-r-xl rounded-l-none">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
                <span class="sr-only">Search</span>
            </button>

        </div>

        @if(isset($documents))
        <h2 class="text-xl font-semibold mt-4">Results:</h2>
        @if($documents->isEmpty())
        <p class="text-gray-500">No documents found.</p>
        @else
        <ul class="list-disc pl-5">
            @foreach($documents as $document)
            <li class="text-gray-700">{{ $document->title }}</li>
            @endforeach
        </ul>
        @endif
        @endif
    </form>
</body>

</html>