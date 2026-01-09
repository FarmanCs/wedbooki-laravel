@php
    $importantData = $importantData ?? $getBusinessImportantData($business);
    $showLink = $showLink ?? true;
@endphp

    <!-- Enhanced Business Card -->
<div class="carousel-card group">
    <!-- Top Section with Image and Overlay -->
    <div class="relative h-48 rounded-lg overflow-hidden mb-4">
        @if($business->profile_image)
            <img src="{{ asset('storage/' . $business->profile_image) }}"
                 alt="{{ $business->company_name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
        @else
            <div class="w-full h-full bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ substr($business->company_name, 0, 2) }}</span>
            </div>
        @endif

        <!-- Featured Badge -->
        @if($importantData['is_featured'])
            <div class="absolute top-3 left-3 bg-yellow-500 text-yellow-900 px-2 py-1 rounded-full text-xs font-bold">
                ★ Featured
            </div>
        @endif

        <!-- Favourite Button -->
        <button wire:click="toggleFavourite({{ $business->id }})"
                class="absolute top-3 right-3 p-2 rounded-full bg-black/50 backdrop-blur-sm hover:bg-black/70 transition-colors">
            @if(in_array($business->id, $favouriteIds))
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            @endif
        </button>

        <!-- Rating Badge -->
        <div class="absolute bottom-3 left-3 flex items-center gap-1 bg-black/70 backdrop-blur-sm px-3 py-1 rounded-full">
            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-white text-sm font-semibold">{{ number_format($importantData['rating'], 1) }}</span>
            <span class="text-gray-300 text-xs">({{ $importantData['reviews_count'] }})</span>
        </div>
    </div>

    <!-- Business Info -->
    <div class="flex-1">
        <h3 class="carousel-card-title line-clamp-1">
            {{ $business->company_name }}
        </h3>

        <!-- Category Badge -->
        <div class="mb-3">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">
                {{ $business->category->type ?? 'Vendor' }}
            </span>
        </div>

        <!-- Description -->
        <p class="carousel-card-description line-clamp-2">
            {{ $importantData['description_short'] }}
        </p>

        <!-- Important Stats -->
        <div class="grid grid-cols-2 gap-3 mt-4 mb-6">
            <!-- Location -->
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-gray-300 line-clamp-1">{{ $importantData['location'] }}</span>
            </div>

            <!-- Experience -->
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-gray-300">{{ $importantData['experience'] }} yrs</span>
            </div>

            <!-- Packages -->
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-gray-300">{{ $importantData['packages_count'] }} packages</span>
            </div>

            <!-- Capacity -->
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 1.5l-5.5 5.5m0 0l-5.5-5.5m5.5 5.5V6"/>
                </svg>
                <span class="text-gray-300">{{ $importantData['capacity'] }}</span>
            </div>
        </div>
    </div>

    <!-- View Details Button -->
    <a href="{{ route('host.vendors.index', $business->id) }}"
       class="w-full custom-button">
        View Details
    </a>
</div>
