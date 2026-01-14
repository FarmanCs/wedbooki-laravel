<div class="min-h-screen w-full">
    <!-- Hero Section with Search -->
    <div class="hero rounded-2xl mb-8">
        <div class="relative px-6 py-12">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Find Perfect Vendors for Your Wedding
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                    Discover curated vendors for every aspect of your special day
                </p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <div
                        class="flex items-center gap-2 border border-zinc-300 dark:border-zinc-600 rounded-xl px-3 py-2 bg-white dark:bg-zinc-800">
                        <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder='Try "Four Seasons" or "Wedding Cake" or "Photographer"'
                            class="w-full outline-none bg-transparent text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-500 dark:placeholder:text-zinc-400"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Category Filter -->
    @if($categories->isNotEmpty())
        <div class="mb-8">
            <div class="vendor-section-header mb-6">
                <h2 class="vendor-section-title">Browse by Category</h2>
            </div>

            <!-- Category Dropdown -->
            <div class="max-w-xs">
                <div class="vendor-category-select-wrapper">
                    <select wire:model.live="categoryFilter" class="vendor-category-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->type }}</option>
                        @endforeach
                    </select>
                    <svg class="vendor-category-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>
    @endif



    <!-- Carousel View for All Categories -->
    @if($viewMode === 'carousel')
        @if(count($categoryData) > 0)
            @foreach($categoryData as $data)
                @php
                    $category = $data['category'];
                    $businesses = $data['businesses'];
                    $position = $this->carouselPositions[$category->id] ?? 0;
                    $totalBusinesses = $businesses->count();
                    $showCarousel = $totalBusinesses > $maxCardsBeforeCarousel;
                @endphp

                <div class="carousel-section mb-12">
                    <!-- Category Header -->
                    <div class="vendor-section-header mb-6">
                        <div>
                            <h2 class="vendor-section-title">{{ $category->type }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                {{ $category->description ?? "Discover the best " . strtolower($category->type) . " for your special day" }}
                            </p>
                        </div>
                        @if($businesses->count() > 0)
                            <button wire:click="$set('categoryFilter', {{ $category->id }})"
                                    class="vendor-section-link">
                                View all ({{ $businesses->count() }}) →
                            </button>
                        @endif
                    </div>

                    @if($businesses->count() > 0)
                        @if($showCarousel)
                            <!-- Carousel Container -->
                            <div class="carousel-container relative" id="carousel-container-{{ $category->id }}">
                                <!-- Carousel Track Container -->
                                <div class="overflow-hidden">
                                    <div class="carousel-track flex transition-transform duration-500 ease-in-out"
                                         style="transform: translateX(calc(-{{ $position * (100 / $cardsPerView) }}%));">
                                        @foreach($businesses as $index => $business)
                                            @php
                                                $importantData = $getBusinessImportantData($business);
                                            @endphp
                                            <div class="carousel-slide">
                                                <div class="h-full px-2">
                                                    @include('components.common.business-card', [
                                                        'business' => $business,
                                                        'importantData' => $importantData,
                                                        'favouriteIds' => $favouriteIds,
                                                        'categoryId' => $category->id
                                                    ])
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                @if($totalBusinesses > $cardsPerView)
                                    <button wire:click="prevCarousel({{ $category->id }})"
                                            class="carousel-nav-btn carousel-prev"
                                            {{ $position === 0 ? 'disabled' : '' }}
                                            aria-label="Previous slide">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>

                                    <button wire:click="nextCarousel({{ $category->id }})"
                                            class="carousel-nav-btn carousel-next"
                                            {{ $position >= ($totalBusinesses - $cardsPerView) ? 'disabled' : '' }}
                                            aria-label="Next slide">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                        @else
                            <!-- Flex container for 4 or fewer items -->
                            @php
                                $containerClass = 'flex justify-center items-start flex-wrap gap-6';
                                if ($totalBusinesses === 1) {
                                    $containerClass .= ' single-item';
                                }
                            @endphp

                            <div class="{{ $containerClass }}">
                                @foreach($businesses as $business)
                                    @php
                                        $importantData = $getBusinessImportantData($business);
                                    @endphp
                                    <div class="@if($totalBusinesses === 1) mx-auto @endif">
                                        @include('components.common.business-card', [
                                            'business' => $business,
                                            'importantData' => $importantData,
                                            'favouriteIds' => $favouriteIds,
                                            'categoryId' => $category->id,
                                            'showLink' => $totalBusinesses > 1
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="vendor-empty-state py-12">
                            <h3 class="empty-state-title">No {{ strtolower($category->type) }} found</h3>
                            <p class="empty-state-description">Check back later for new vendors</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="vendor-empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="empty-state-title">No vendors found</h3>
                <p class="empty-state-description">
                    @if($search)
                        No results for "{{ $search }}". Try a different search term or clear the search.
                    @else
                        No vendors available. Try selecting a category from the dropdown above.
                    @endif
                </p>
            </div>
        @endif
    @endif

    <!-- Grid View for Single Category -->
    @if($viewMode === 'grid' && $categoryFilter)
        <div class="vendor-section-header mb-6">
            <div>
                <h2 class="vendor-section-title">
                    {{ $categories->firstWhere('id', $categoryFilter)->type ?? 'Selected Category' }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $totalBusinesses }} {{ Str::plural('vendor', $totalBusinesses) }} found
                    @if($search)
                        matching "{{ $search }}"
                    @endif
                </p>
            </div>
        </div>

        @if($usePagination ? $businesses->count() > 0 : $businesses->isNotEmpty())
            @if($businesses->count() === 1)
                <!-- Single item - centered -->
                <div class="flex justify-center mb-8">
                    <div class="w-[300px]">
                        @php
                            $business = $businesses->first();
                            $importantData = $getBusinessImportantData($business);
                        @endphp
                        @include('components.common.business-card', [
                            'business' => $business,
                            'importantData' => $importantData,
                            'favouriteIds' => $favouriteIds,
                            'categoryId' => $categoryFilter,
                            'showLink' => false
                        ])
                    </div>
                </div>
            @elseif($businesses->count() < 4)
                <!-- 2-3 items - equally spaced -->
                <div class="flex flex-wrap justify-center gap-6 mb-8">
                    @foreach($businesses as $business)
                        @php
                            $importantData = $getBusinessImportantData($business);
                        @endphp
                        <div>
                            @include('components.common.business-card', [
                                'business' => $business,
                                'importantData' => $importantData,
                                'favouriteIds' => $favouriteIds,
                                'categoryId' => $categoryFilter,
                                'showLink' => true
                            ])
                        </div>
                    @endforeach
                </div>
            @else
                <!-- 4+ items - grid layout -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($businesses as $business)
                        @php
                            $importantData = $getBusinessImportantData($business);
                        @endphp
                        <div class="flex justify-center">
                            @include('components.common.business-card', [
                                'business' => $business,
                                'importantData' => $importantData,
                                'favouriteIds' => $favouriteIds,
                                'categoryId' => $categoryFilter,
                                'showLink' => true
                            ])
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Livewire Pagination -->
            @if($usePagination)
                <div class="pagination-wrapper mt-8">
                    {{ $businesses->links('livewire.custom-pagination') }}
                </div>
            @endif
        @else
            <div class="vendor-empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="empty-state-title">No vendors found</h3>
                <p class="empty-state-description">
                    @if($search)
                        No results for "{{ $search }}". Try a different search term.
                    @else
                        No vendors available in this category. Try selecting a different category.
                    @endif
                </p>
            </div>
        @endif
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedBusiness)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75"
                     wire:click="closeDetailModal"
                     aria-hidden="true"></div>

                {{-- Modal Content --}}
                <div class="relative inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-900 shadow-xl rounded-2xl">

                    {{-- Close Button --}}
                    <button wire:click="closeDetailModal"
                            class="absolute top-4 right-4 z-10 p-2 rounded-full bg-black/50 hover:bg-black/70 transition-colors"
                            aria-label="Close modal">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Include the detail view component --}}
                    @include('components.common.details', ['business' => $selectedBusiness, 'favouriteIds' => $favouriteIds])
                </div>
            </div>
        </div>
    @endif
</div>
