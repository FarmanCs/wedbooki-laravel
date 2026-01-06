<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Browse Vendors</h1>
            <p class="mt-1 text-gray-600">Find the perfect vendors for your event</p>
        </div>
        <flux:button href="{{ route('host.vendors.favourites') }}" wire:navigate variant="ghost">
            <flux:icon.heart class="size-5" />
            My Favourites
        </flux:button>
    </div>

    {{-- Filters --}}
    <flux:card class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search vendors..."
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="categoryFilter">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="sortBy">
                <option value="rating">Sort by Rating</option>
                <option value="company_name">Sort by Name</option>
                <option value="created_at">Sort by Newest</option>
            </flux:select>
        </div>
    </flux:card>

    {{-- Vendors Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($businesses as $business)
            <flux:card class="overflow-hidden hover:shadow-lg transition-shadow">
                {{-- Cover Image --}}
                <div class="relative h-48 bg-gray-200">
                    @if($business->cover_image)
                        <img
                            src="{{ asset('storage/' . $business->cover_image) }}"
                            alt="{{ $business->company_name }}"
                            class="w-full h-full object-cover"
                        />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
                            <flux:icon.building-storefront class="size-16 text-white opacity-50" />
                        </div>
                    @endif

                    {{-- Favourite Button --}}
                    <button
                        wire:click="toggleFavourite({{ $business->id }})"
                        class="absolute top-3 right-3 p-2 bg-white rounded-full shadow-md hover:scale-110 transition-transform"
                    >
                        <flux:icon.heart
                            class="size-5 {{ in_array($business->id, $favouriteIds) ? 'text-red-500 fill-red-500' : 'text-gray-400' }}"
                        />
                    </button>

                    {{-- Featured Badge --}}
                    @if($business->is_featured)
                        <div class="absolute top-3 left-3">
                            <flux:badge color="yellow">Featured</flux:badge>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $business->company_name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $business->category->name ?? 'Uncategorized' }}
                            </p>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <flux:icon.star
                                    class="size-4 {{ $i <= ($business->rating ?? 0) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}"
                                />
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ number_format($business->rating ?? 0, 1) }}
                        </span>
                    </div>

                    {{-- Description --}}
                    @if($business->business_desc)
                        <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                            {{ $business->business_desc }}
                        </p>
                    @endif

                    {{-- Packages Info --}}
                    @if($business->packages->isNotEmpty())
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                Starting from
                                <span class="font-semibold text-gray-900">
                                    ${{ number_format($business->packages->min('price'), 2) }}
                                </span>
                            </p>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <flux:button
                            href="{{ route('host.bookings.create', ['business' => $business->id]) }}"
                            wire:navigate
                            variant="primary"
                            class="flex-1"
                        >
                            Book Now
                        </flux:button>
                        <flux:button variant="ghost">
                            View Details
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-3 text-center py-12">
                <flux:icon.building-storefront class="size-16 mx-auto text-gray-400 mb-4" />
                <p class="text-gray-500">No vendors found. Try adjusting your filters.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($businesses->hasPages())
        <div class="flex justify-center">
            {{ $businesses->links() }}
        </div>
    @endif
</div>
