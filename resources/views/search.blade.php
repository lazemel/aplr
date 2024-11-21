<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .results-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .results-container h2 {
            font-size: 2rem;
            color: #4A5568; 
            text-align: center;
        }
        .results-container ul {
            list-style: none;
            padding: 0;
        }
        .results-container li {
            padding: 15px 10px;
            border-bottom: 1px solid #E2E8F0; 
            transition: background-color 0.2s;
        }
        .results-container li:hover {
            background: #F7FAFC; 
        }
        .results-container li h3 {
            font-size: 1.25rem;
            color: #3182CE; 
            margin-bottom: 5px;
        }
        .results-container li p {
            font-size: 0.875rem;
            color: #718096; 
        }
        .results-container li a {
            font-size: 0.875rem;
            color: #3182CE; 
            text-decoration: none;
        }
        .results-container li a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
<div class="results-container">
    <form action="{{ route('documents.search', [], true) }}" method="GET" class="max-w-lg w-full">
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

        <div class="max-w-4xl w-full mx-4 bg-white p-6 shadow-xl rounded-lg">
            @if(isset($documents))
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-700">Search Results</h2>
                @if($documents->isEmpty())
                    <p class="text-lg text-gray-500 text-center">No documents found. Try another search.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($documents as $document)
                            <li class="py-4 hover:bg-gray-50 transition duration-150">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-blue-600">{{ $document->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            by {{ $document->author ?? 'Unknown Author' }}
                                        </p>
                                    </div>
                                    <div>
                                        <a href="#" class="text-sm text-blue-500 hover:underline">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
</div>
</body>
</html>