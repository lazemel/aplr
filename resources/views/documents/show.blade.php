<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }}</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">{{ $document->title }}</h1>

                    @if($document->page_numbers)
                        <div class="mb-4">
                            <h2 class="text-xl font-semibold">Page Numbers:</h2>
                            <div class="mt-2">
                                @foreach($document->page_numbers as $page)
                                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
                                        Page {{ $page }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($document->paragraphs)
                        <div class="mt-6">
                            <h2 class="text-xl font-semibold mb-4">Content:</h2>
                            @foreach($document->paragraphs as $paragraph)
                                <p class="mb-4">{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Back to Search Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 