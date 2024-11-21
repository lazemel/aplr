<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-b from-gray-100 to-gray-200">
    <div class="max-w-4xl mx-auto mt-10 p-6 bg-gray-50 rounded-lg shadow-md">
        @if(isset($documents))
            <h2 class="text-2xl font-semibold mt-4 mb-4 text-center">Search Results:</h2>
            @if($documents->isEmpty())
                <p class="text-gray-500 text-center">No documents found.</p>
            @else
                <ul class="space-y-4">
                    @foreach($documents as $document)
                        <li class="p-4 bg-white shadow-sm rounded-lg hover:shadow-md transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <div>
                                <span class="font-semibold text-lg text-blue-500">{{ $document->title }}</span>
                                <p class="text-sm text-gray-600">by {{ $document->author ?? 'Unknown Author' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif

        <a href="/" class="inline-block mt-6 text-blue-600 hover:text-blue-800 font-medium underline text-center">
            Go back
        </a>
    </div>
</body>

</html>
