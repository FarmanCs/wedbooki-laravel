{{-- resources/views/vendor/livewire/custom-pagination.blade.php --}}
{{--@if ($paginator->hasPages())--}}
{{--    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">--}}
{{--        <div class="flex items-center gap-2">--}}
{{--            --}}{{----}}{{-- Previous Page Link --}}
{{--            @if ($paginator->onFirstPage())--}}
{{--                <span class="pagination-link pagination-link-disabled">--}}
{{--                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>--}}
{{--                    </svg>--}}
{{--                </span>--}}
{{--            @else--}}
{{--                <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="pagination-link">--}}
{{--                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>--}}
{{--                    </svg>--}}
{{--                </button>--}}
{{--            @endif--}}

{{--            --}}{{----}}{{-- Pagination Elements --}}
{{--            @foreach ($elements as $element)--}}
{{--                --}}{{----}}{{-- "Three Dots" Separator --}}
{{--                @if (is_string($element))--}}
{{--                    <span class="pagination-link pagination-link-disabled">{{ $element }}</span>--}}
{{--                @endif--}}

{{--                --}}{{----}}{{-- Array Of Links --}}
{{--                @if (is_array($element))--}}
{{--                    @foreach ($element as $page => $url)--}}
{{--                        @if ($page == $paginator->currentPage())--}}
{{--                            <span class="pagination-link pagination-link-active" aria-current="page">{{ $page }}</span>--}}
{{--                        @else--}}
{{--                            <button wire:click="gotoPage({{ $page }})" class="pagination-link">{{ $page }}</button>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            @endforeach--}}

{{--            --}}{{----}}{{-- Next Page Link --}}
{{--            @if ($paginator->hasMorePages())--}}
{{--                <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="pagination-link">--}}
{{--                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>--}}
{{--                    </svg>--}}
{{--                </button>--}}
{{--            @else--}}
{{--                <span class="pagination-link pagination-link-disabled">--}}
{{--                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>--}}
{{--                    </svg>--}}
{{--                </span>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </nav>--}}

{{--    --}}{{----}}{{-- Results Info --}}
{{--    <p class="pagination-info">--}}
{{--        Showing <span class="font-semibold">{{ $paginator->firstItem() }}</span>--}}
{{--        to <span class="font-semibold">{{ $paginator->lastItem() }}</span>--}}
{{--        of <span class="font-semibold">{{ $paginator->total() }}</span> results--}}
{{--    </p>--}}
{{--@endif--}}

{{-- resources/views/vendor/livewire/custom-pagination.blade.php --}}
{{--@if ($paginator->hasPages())--}}
{{--    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">--}}
{{--        <!-- Page Info -->--}}
{{--        <div class="text-sm text-zinc-600 dark:text-zinc-400">--}}
{{--            Showing--}}
{{--            <span class="font-semibold text-zinc-900 dark:text-zinc-100">--}}
{{--                {{ $paginator->firstItem() ?? 0 }}--}}
{{--            </span>--}}
{{--            to--}}
{{--            <span class="font-semibold text-zinc-900 dark:text-zinc-100">--}}
{{--                {{ $paginator->lastItem() ?? 0 }}--}}
{{--            </span>--}}
{{--            of--}}
{{--            <span class="font-semibold text-zinc-900 dark:text-zinc-100">--}}
{{--                {{ $paginator->total() }}--}}
{{--            </span>--}}
{{--            results--}}
{{--        </div>--}}

{{--        <!-- Pagination Navigation -->--}}
{{--        <nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">--}}
{{--            <div class="flex items-center gap-1">--}}
{{--                --}}{{-- First Page Link --}}
{{--                @if (!$paginator->onFirstPage())--}}
{{--                    <button--}}
{{--                        wire:click="gotoPage(1)"--}}
{{--                        wire:loading.attr="disabled"--}}
{{--                        class="pagination-link pagination-link-first"--}}
{{--                        aria-label="Go to first page"--}}
{{--                    >--}}
{{--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                @else--}}
{{--                    <span class="pagination-link pagination-link-disabled pagination-link-first">--}}
{{--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                @endif--}}

{{--                --}}{{-- Previous Page Link --}}
{{--                @if ($paginator->onFirstPage())--}}
{{--                    <span class="pagination-link pagination-link-disabled">--}}
{{--                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                @else--}}
{{--                    <button--}}
{{--                        wire:click="previousPage"--}}
{{--                        wire:loading.attr="disabled"--}}
{{--                        rel="prev"--}}
{{--                        class="pagination-link"--}}
{{--                        aria-label="Go to previous page"--}}
{{--                    >--}}
{{--                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                @endif--}}

{{--                --}}{{-- Pagination Elements --}}
{{--                @php--}}
{{--                    // Show limited page numbers for better UX--}}
{{--                    $current = $paginator->currentPage();--}}
{{--                    $last = $paginator->lastPage();--}}
{{--                    $showPages = 5;--}}
{{--                    $start = max(1, $current - floor($showPages / 2));--}}
{{--                    $end = min($last, $start + $showPages - 1);--}}

{{--                    if ($end - $start < $showPages - 1) {--}}
{{--                        $start = max(1, $end - $showPages + 1);--}}
{{--                    }--}}
{{--                @endphp--}}

{{--                @if ($start > 1)--}}
{{--                    <button--}}
{{--                        wire:click="gotoPage(1)"--}}
{{--                        class="pagination-link"--}}
{{--                    >--}}
{{--                        1--}}
{{--                    </button>--}}
{{--                    @if ($start > 2)--}}
{{--                        <span class="pagination-link pagination-link-disabled">...</span>--}}
{{--                    @endif--}}
{{--                @endif--}}

{{--                @for ($page = $start; $page <= $end; $page++)--}}
{{--                    @if ($page == $paginator->currentPage())--}}
{{--                        <span class="pagination-link pagination-link-active" aria-current="page">--}}
{{--                            {{ $page }}--}}
{{--                        </span>--}}
{{--                    @else--}}
{{--                        <button--}}
{{--                            wire:click="gotoPage({{ $page }})"--}}
{{--                            class="pagination-link"--}}
{{--                        >--}}
{{--                            {{ $page }}--}}
{{--                        </button>--}}
{{--                    @endif--}}
{{--                @endfor--}}

{{--                @if ($end < $last)--}}
{{--                    @if ($end < $last - 1)--}}
{{--                        <span class="pagination-link pagination-link-disabled">...</span>--}}
{{--                    @endif--}}
{{--                    <button--}}
{{--                        wire:click="gotoPage({{ $last }})"--}}
{{--                        class="pagination-link"--}}
{{--                    >--}}
{{--                        {{ $last }}--}}
{{--                    </button>--}}
{{--                @endif--}}

{{--                --}}{{-- Next Page Link --}}
{{--                @if ($paginator->hasMorePages())--}}
{{--                    <button--}}
{{--                        wire:click="nextPage"--}}
{{--                        wire:loading.attr="disabled"--}}
{{--                        rel="next"--}}
{{--                        class="pagination-link"--}}
{{--                        aria-label="Go to next page"--}}
{{--                    >--}}
{{--                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                @else--}}
{{--                    <span class="pagination-link pagination-link-disabled">--}}
{{--                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                @endif--}}

{{--                --}}{{-- Last Page Link --}}
{{--                @if ($paginator->hasMorePages())--}}
{{--                    <button--}}
{{--                        wire:click="gotoPage({{ $paginator->lastPage() }})"--}}
{{--                        wire:loading.attr="disabled"--}}
{{--                        class="pagination-link pagination-link-last"--}}
{{--                        aria-label="Go to last page"--}}
{{--                    >--}}
{{--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                @else--}}
{{--                    <span class="pagination-link pagination-link-disabled pagination-link-last">--}}
{{--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </nav>--}}
{{--    </div>--}}
{{--@endif--}}
