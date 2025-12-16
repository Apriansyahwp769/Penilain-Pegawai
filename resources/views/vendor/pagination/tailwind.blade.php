<div class="flex justify-between items-center mt-4">
    <div class="text-sm text-gray-500">
        {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} hasil
    </div>

    <ul class="flex space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li>
                <span class="px-3 py-2 text-gray-400 rounded-full cursor-not-allowed hover:bg-gray-50">←</span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-gray-700 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-colors">←</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="px-3 py-2 text-gray-400">...</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li>
                            <span class="px-3 py-2 bg-blue-600 text-white font-medium rounded-full">{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}" class="px-3 py-2 text-gray-700 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-colors">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-gray-700 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-colors">→</a>
            </li>
        @else
            <li>
                <span class="px-3 py-2 text-gray-400 rounded-full cursor-not-allowed hover:bg-gray-50">→</span>
            </li>
        @endif
    </ul>
</div>