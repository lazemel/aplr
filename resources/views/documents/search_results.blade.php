<!-- resources/views/documents/search_results.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-800">
                    <a href="/">Coin Search Engine</a>
                </div>
                <!-- Search Form -->
                <form action="{{ route('documents.search') }}" method="GET" class="flex-1 max-w-2xl mx-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="query" 
                            value="{{ request('query') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500"
                            placeholder="Search documents..."
                        >
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        @if(request('query'))
            <h2 class="text-xl font-semibold text-gray-700 mb-6">
                Search results for: "{{ request('query') }}"
                <span class="text-sm text-gray-500 ml-2">
                    (Page {{ $documents->currentPage() }} of {{ $documents->lastPage() }})
                </span>
            </h2>
        @endif

        <!-- Results List -->
        <div class="space-y-4">
            @forelse($documents as $document)
                <div class="bg-white p-4 border-b hover:bg-gray-50 transition-colors duration-150">
                    <a href="{{ route('documents.show', $document->id) }}" class="block">
                        <h3 class="text-lg font-semibold text-blue-600 hover:text-blue-800 mb-2">
                            {{ $document->title }}
                        </h3>
                        
                        @if($document->paragraphs)
                            <p class="text-gray-600 mb-2">
                                @php
                                    if (is_array($document->paragraphs)) {
                                        $paragraphText = implode(' ', $document->paragraphs);
                                    } else {
                                        $paragraphText = $document->paragraphs;
                                    }
                                    $limitedText = Str::limit($paragraphText, 200);
                                @endphp
                                {{ $limitedText }}
                            </p>
                        @endif

                        @if($document->page_numbers)
                            <div class="flex flex-wrap gap-2">
                                @foreach($document->page_numbers as $page)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                        Page {{ $page }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No results found</h3>
                    <p class="text-gray-500">Try adjusting your search terms</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->total() > 0)
            <div class="mt-6">
                {{ $documents->appends(['query' => request('query')])->links() }}
            </div>
        @endif
    </div>
</body>
</html>
