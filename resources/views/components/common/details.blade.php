{{-- resources/views/components/common/vendor-detail.blade.php --}}

<div class="max-h-[90vh] overflow-y-auto">
    {{-- Hero Section --}}
    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-8">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- Vendor Image --}}
            <div class="w-full lg:w-1/3">
                <div class="relative rounded-xl overflow-hidden shadow-2xl">
                    @if($business->profile_image)
                        <img src="{{ asset('storage/' . $business->profile_image) }}"
                             alt="{{ $business->company_name }}"
                             class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-purple-700 to-pink-700 flex items-center justify-center">
                            <span class="text-white text-6xl font-bold">{{ substr($business->company_name, 0, 2) }}</span>
                        </div>
                    @endif

                    @if($business->is_featured)
                        <div class="absolute top-4 left-4 bg-yellow-400 text-gray-900 px-3 py-1.5 rounded-lg text-sm font-semibold shadow-lg">
                            ★ Featured Vendor
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vendor Info --}}
            <div class="flex-1 text-white">
                <h1 class="text-4xl font-bold mb-2">{{ $business->company_name }}</h1>

                {{-- Rating --}}
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= ($business->rating ?? 0) ? 'text-yellow-400' : 'text-white/30' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-lg font-semibold">{{ number_format($business->rating ?? 0, 1) }}</span>
                    <span class="text-sm opacity-80">({{ $business->reviews_count ?? 0 }} reviews)</span>
                </div>

                <div class="inline-block px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg mb-4">
                    {{ $business->category->type ?? 'Vendor' }}
                </div>

                @if($business->tagline)
                    <p class="text-lg italic mb-4">{{ $business->tagline }}</p>
                @endif

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm opacity-80">Location</p>
                        <p class="font-semibold">{{ $business->city }}, {{ $business->country }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Experience</p>
                        <p class="font-semibold">{{ $business->vendor->years_of_experience ?? 'N/A' }} Years</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Team Size</p>
                        <p class="font-semibold">{{ $business->team_size ?? '15' }} Members</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Packages</p>
                        <p class="font-semibold">{{ $business->packages_count ?? 0 }} Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="p-6">
        {{-- Tabs --}}
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="flex gap-4 overflow-x-auto">
                <button onclick="switchTab('about')"
                        class="tab-button active px-4 py-2 font-medium border-b-2 border-transparent hover:border-purple-600">
                    About
                </button>
                <button onclick="switchTab('packages')"
                        class="tab-button px-4 py-2 font-medium border-b-2 border-transparent hover:border-purple-600">
                    Packages
                </button>
                <button onclick="switchTab('services')"
                        class="tab-button px-4 py-2 font-medium border-b-2 border-transparent hover:border-purple-600">
                    Services
                </button>
                <button onclick="switchTab('reviews')"
                        class="tab-button px-4 py-2 font-medium border-b-2 border-transparent hover:border-purple-600">
                    Reviews
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="tab-content-wrapper">
            {{-- About Tab --}}
            <div id="tab-about" class="tab-content">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">About This Vendor</h2>

                @if($business->vendor)
                    <div class="flex items-center gap-4 mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($business->vendor->name ?? 'HK', 0, 2) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $business->vendor->name ?? 'Business Owner' }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Owner</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $business->vendor->years_of_experience ?? 8 }} Years of Experience
                            </p>
                        </div>
                    </div>
                @endif

                <div class="prose prose-gray dark:prose-invert max-w-none">
                    <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $business->business_desc ?? 'Premium services tailored to make your special day unforgettable.' }}
                    </p>
                </div>

                {{-- Business Details --}}
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Service Areas</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $business->city }}, {{ $business->country }} and surrounding areas
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Capacity</h4>
                        <p class="text-gray-600 dark:text-gray-400">{{ $business->capacity ?? 'Contact for details' }}</p>
                    </div>
                </div>
            </div>

            {{-- Packages Tab --}}
            <div id="tab-packages" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Packages & Pricing</h2>

                @if($business->packages && $business->packages->count() > 0)
                    <div class="space-y-4">
                        @foreach($business->packages as $package)
                            <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                            {{ $package->package_name }}
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                                            {{ $package->description ?? 'Premium package for your special day' }}
                                        </p>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                                Rs {{ number_format($package->price ?? 13155.18, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <button class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg transition-colors">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No packages available at the moment. Contact vendor for custom pricing.</p>
                    </div>
                @endif
            </div>

            {{-- Services Tab --}}
            <div id="tab-services" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Services Offered</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    @php
                        $staticServices = [
                            ['name' => 'Beverage & Mocktail Bar', 'desc' => 'Refreshing drinks and specialty beverages.', 'price' => 150344.88],
                            ['name' => 'Dessert & Sweet Table', 'desc' => 'Customized dessert spreads and traditional sweets.', 'price' => 187931.10],
                            ['name' => 'Plated Service', 'desc' => 'Individually plated gourmet meals.', 'price' => 13155.18],
                            ['name' => 'Buffet Service', 'desc' => 'Wide variety of dishes served buffet-style.', 'price' => 25000.00]
                        ];
                    @endphp

                    @foreach($staticServices as $service)
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $service['name'] }}</h3>
                                <button class="px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Book Now
                                </button>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $service['desc'] }}</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                Rs {{ number_format($service['price'], 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Reviews Tab --}}
            <div id="tab-reviews" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Customer Reviews</h2>

                {{-- Static reviews for now --}}
                <div class="space-y-6">
                    @php
                        $staticReviews = [
                            ['name' => 'Sarah Johnson', 'rating' => 5, 'date' => '2 weeks ago', 'comment' => 'Absolutely amazing service! The food was delicious and the presentation was stunning.'],
                            ['name' => 'Michael Chen', 'rating' => 4, 'date' => '1 month ago', 'comment' => 'Great experience overall. The team was professional and accommodating.'],
                            ['name' => 'Priya Sharma', 'rating' => 5, 'date' => '2 months ago', 'comment' => 'Exceeded all expectations! Highly recommend!']
                        ];
                    @endphp

                    @foreach($staticReviews as $review)
                        <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                    {{ substr($review['name'], 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $review['name'] }}</h4>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $review['date'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">{{ $review['comment'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-purple-600', 'text-purple-600');
            button.classList.add('text-gray-600', 'dark:text-gray-400');
        });

        // Show selected tab content
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        // Add active class to clicked button
        event.target.classList.add('active', 'border-purple-600', 'text-purple-600');
        event.target.classList.remove('text-gray-600', 'dark:text-gray-400');
    }
</script>

<style>
    .tab-button.active {
        @apply border-purple-600 text-purple-600 dark:text-purple-400;
    }
</style>
