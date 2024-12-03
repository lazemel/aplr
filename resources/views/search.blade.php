<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-100">
    <div class="w-full max-w-4xl flex flex-col items-center p-6">
        <h1 class="mb-8 text-7xl md:text-8xl lg:text-9xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white text-center">
            Coin<span class="text-green-800">Search</span>
        </h1>
        
        <h2 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl dark:text-white text-center">
            Search <mark class="px-2 text-white bg-green-800 rounded dark:bg-green-700">Documents</mark> with Ease
        </h2>
        <p class="text-sm font-normal text-gray-500 lg:text-base dark:text-gray-400 text-center mb-8">
            Discover and explore a wide range of documents with ease and efficiency.
        </p>
        <form action="{{ route('documents.search', [], true) }}" method="GET" class="max-w-lg w-full">
            <div class="flex">
                <input type="search" name="query" placeholder="Search Documents..." required
                    class="block p-4 w-full text-sm text-gray-900 bg-gray-50 rounded-l-full border border-gray-300 focus:ring-2 focus:ring-green-500 placeholder-gray-500" />
                <button type="submit" class="flex-shrink-0 p-4 text-sm font-medium text-white bg-green-800 border border-green-800 hover:bg-green-900 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-r-full">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="sr-only">Search</span>
                </button>

            </div>

            <!-- <div class="max-w-4xl w-full mx-4 bg-white p-6 shadow-xl rounded-lg">
                @if(isset($documents))
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-700">Search Results</h2>
                @if($documents->isEmpty())
                <p class="text-lg text-gray-500 text-center">No documents found. Try another search.</p>
                @else
                <ul class="divide-y divide-gray-200">
                    @foreach($documents as $document)
                    <li class="py-4 hover:bg-gray-50 transition duration-150">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-600">{{ $document->title }}</h3>
                            <p class="text-sm text-gray-500">
                                by {{ $document->author ?? 'Unknown Author' }}
                            </p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
                @endif
                <a href="/" class="inline-block mt-6 text-blue-600 hover:text-blue-800 font-medium underline text-center">
                    Go back
                </a>
            </div> -->
    </div>
    <footer class="fixed bottom-0 w-full py-4 bg-white bg-opacity-80 shadow-md">
        <div class="text-center text-sm text-gray-600">
            Powered by 
            <a href="https://cloud.typesense.org" class="text-green-800 hover:text-green-900 font-medium">Typesense Cloud</a> 
            and 
            <a href="https://laravel.com/docs/scout" class="text-green-800 hover:text-green-900 font-medium">Laravel Scout</a>
        </div>
    </footer>
</body>

</html>
</html>