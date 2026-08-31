@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="flex flex-col sm:flex-row items-center justify-between gap-3">

        {{-- Phones: previous / next with a page indicator between them --}}
        <div class="flex sm:hidden w-full items-center justify-between gap-2">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex items-center gap-1 px-3.5 py-2.5 text-xs font-bold text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-default select-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    {{ __('Prev') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1 px-3.5 py-2.5 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-xl active:bg-gray-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    {{ __('Prev') }}
                </a>
            @endif

            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                {{ __('Page') }} {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1 px-3.5 py-2.5 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-xl active:bg-gray-100 transition-colors">
                    {{ __('Next') }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex items-center gap-1 px-3.5 py-2.5 text-xs font-bold text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-default select-none">
                    {{ __('Next') }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </div>

        {{-- Tablet and up: result count on the left, numbered links on the right --}}
        <p class="hidden sm:block text-xs font-semibold text-gray-500">
            {!! __('Showing') !!}
            <span class="font-extrabold text-gray-900">{{ $paginator->firstItem() }}</span>
            {!! __('to') !!}
            <span class="font-extrabold text-gray-900">{{ $paginator->lastItem() }}</span>
            {!! __('of') !!}
            <span class="font-extrabold text-gray-900">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="hidden sm:flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-300 bg-white border border-gray-200 cursor-default select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-gray-400 select-none">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-xl text-xs font-extrabold text-white bg-blue-600 border border-blue-600 shadow-sm shadow-blue-500/20 select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-blue-600 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-300 bg-white border border-gray-200 cursor-default select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
