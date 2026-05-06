@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark_text-gray-600 dark_bg-gray-800 dark_border-gray-600">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover_text-gray-500 focus_outline-none focus_ring ring-gray-300 focus_border-blue-300 active_bg-gray-100 active_text-gray-700 transition ease-in-out duration-150 dark_bg-gray-800 dark_border-gray-600 dark_text-gray-300 dark_focus_border-blue-700 dark_active_bg-gray-700 dark_active_text-gray-300">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover_text-gray-500 focus_outline-none focus_ring ring-gray-300 focus_border-blue-300 active_bg-gray-100 active_text-gray-700 transition ease-in-out duration-150 dark_bg-gray-800 dark_border-gray-600 dark_text-gray-300 dark_focus_border-blue-700 dark_active_bg-gray-700 dark_active_text-gray-300">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark_text-gray-600 dark_bg-gray-800 dark_border-gray-600">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
