<div class="min-h-screen bg-gradient-to-br from-zinc-50 via-white to-purple-50 dark:from-zinc-950 dark:via-zinc-900 dark:to-purple-950/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                        Welcome back, {{ $host->full_name }} 👋
                    </h1>
                    <p class="mt-2 text-base text-zinc-600 dark:text-zinc-400">
                        Here's what's happening with your wedding planning
                    </p>
                </div>

                @if($host->wedding_date && isset($weddingTimeline['days_to_wedding']) && $weddingTimeline['days_to_wedding'] > 0)
                    <div class="group relative overflow-hidden inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <svg class="relative w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="relative">
                            <p class="text-sm font-semibold">{{ $weddingTimeline['days_to_wedding'] }} days until your big day!</p>
                            <p class="text-xs opacity-90">{{ $weddingTimeline['wedding_date_formatted'] }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <!-- Total Bookings -->
                <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm hover:shadow-xl border border-zinc-200 dark:border-zinc-800 transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 dark:bg-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Bookings</p>
                            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalBookings }}</p>
                            <div class="mt-3 flex items-center text-xs space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $completedBookings }} done
                                </span>
                                <span class="text-zinc-400">•</span>
                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $upcomingBookingsCount }} upcoming</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg group-hover:shadow-blue-500/50 transition-shadow duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm hover:shadow-xl border border-zinc-200 dark:border-zinc-800 transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 dark:bg-purple-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Upcoming Events</p>
                            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $upcomingBookingsCount }}</p>
                            @if($upcomingBookings->isNotEmpty())
                                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                                    Next: <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $upcomingBookings->first()->event_date->format('M d, Y') }}</span>
                                </p>
                            @else
                                <p class="mt-3 text-xs text-zinc-400">No upcoming events</p>
                            @endif
                        </div>
                        <div class="p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg group-hover:shadow-purple-500/50 transition-shadow duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm hover:shadow-xl border border-zinc-200 dark:border-zinc-800 transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 dark:bg-amber-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $pendingTasksCount }}</p>
                            <div class="mt-3 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-1.5 rounded-full transition-all duration-500"
                                     style="width: {{ min(100, ($pendingTasksCount > 0 ? ($pendingTasksCount / 20) * 100 : 0)) }}%"></div>
                            </div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg group-hover:shadow-amber-500/50 transition-shadow duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Favorites -->
                <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm hover:shadow-xl border border-zinc-200 dark:border-zinc-800 transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-pink-500/5 dark:bg-pink-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Favorite Vendors</p>
                            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $favoritesCount }}</p>
                            <a href="{{ route('host.vendors.index') }}" class="mt-3 inline-flex items-center text-xs font-semibold text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 transition-colors">
                                Browse more
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl shadow-lg group-hover:shadow-pink-500/50 transition-shadow duration-300">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Upcoming Bookings -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                    <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-zinc-50 to-white dark:from-zinc-800 dark:to-zinc-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Upcoming Bookings</h2>
                            </div>
                            <a href="{{ route('host.bookings.index') }}" class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
                                View all →
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($upcomingBookings as $booking)
                            <div class="px-6 py-5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all duration-200 group">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center space-x-4 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm group-hover:shadow-md transition-shadow">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-zinc-900 dark:text-white truncate">
                                                {{ $booking->business->company_name ?? 'N/A' }}
                                            </h3>
                                            <div class="mt-1 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                    {{ $booking->package->name ?? 'Custom Package' }}
                                                </span>
                                                <span class="text-zinc-400">•</span>
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $booking->event_date->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                               ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                               'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <p class="mt-2 text-sm font-bold text-zinc-900 dark:text-white">
                                            ${{ number_format($booking->amount, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <div class="mx-auto w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">No upcoming bookings</h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Start booking vendors to make your wedding perfect.</p>
                                <div class="mt-6">
                                    <a href="{{ route('host.vendors.index') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        Browse Vendors
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                    <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-zinc-50 to-white dark:from-zinc-800 dark:to-zinc-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Pending Tasks</h2>
                            </div>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                {{ $pendingTasksCount }} pending
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($pendingTasks as $task)
                            <div class="px-6 py-5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <button
                                            wire:click="markTaskComplete({{ $task->id }})"
                                            class="flex-shrink-0 w-6 h-6 mt-0.5 rounded-lg border-2 border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 cursor-pointer group/checkbox">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 opacity-0 group-hover/checkbox:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium text-zinc-900 dark:text-white">
                                                {{ $task->task_name }}
                                            </h3>
                                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                                <span class="inline-flex items-center text-sm text-zinc-500 dark:text-zinc-400">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($task->check_list_due_date)->format('M d, Y') }}
                                                </span>
                                                @if($task->category)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300">
                                                        {{ $task->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="flex-shrink-0 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                        {{ \Carbon\Carbon::parse($task->check_list_due_date)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">All caught up!</h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">You have no pending tasks. Great job! 🎉</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="space-y-8">
                <!-- Wedding Timeline -->
                @if($host->wedding_date && isset($weddingTimeline['is_past']) && !$weddingTimeline['is_past'])
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden sticky top-24">
                        <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Wedding Timeline</h2>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                                        {{ $weddingTimeline['days_to_wedding'] }} days to go
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <div class="space-y-5">
                                @foreach($weddingTimeline['milestones'] as $milestone)
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 relative">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                {{ $milestone['completed'] ? 'bg-gradient-to-br from-green-500 to-emerald-500 shadow-lg' : 'bg-zinc-100 dark:bg-zinc-700' }}">
                                                @if($milestone['completed'])
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @else
                                                    <div class="w-3 h-3 rounded-full {{ $milestone['date']->isPast() ? 'bg-amber-500' : 'bg-zinc-300 dark:bg-zinc-500' }}"></div>
                                                @endif
                                            </div>
                                            @if(!$loop->last)
                                                <div class="absolute top-10 left-1/2 -translate-x-1/2 w-0.5 h-5 {{ $milestone['completed'] ? 'bg-green-300 dark:bg-green-700' : 'bg-zinc-200 dark:bg-zinc-700' }}"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 pt-1.5">
                                            <p class="text-sm font-semibold {{ $milestone['completed'] ? 'text-zinc-900 dark:text-white' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                {{ $milestone['label'] }}
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                                {{ $milestone['date']->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-purple-600 via-purple-500 to-pink-500 rounded-2xl p-6 text-white shadow-xl overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                    <div class="relative">
                        <h3 class="text-xl font-bold">Quick Actions</h3>
                        <p class="mt-1 text-sm text-purple-100">Jump to common tasks</p>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <a href="{{ route('host.vendors.index') }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-4 text-center transition-all duration-200 hover:scale-105 group">
                                <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">New Booking</span>
                            </a>

                            <a href="{{ route('host.checklists.index') }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-4 text-center transition-all duration-200 hover:scale-105 group">
                                <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">Checklist</span>
                            </a>

                            <a href="{{ route('host.guests.index') }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-4 text-center transition-all duration-200 hover:scale-105 group">
                                <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">Guests</span>
                            </a>

                            <a href="{{ route('host.vendors.index') }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-4 text-center transition-all duration-200 hover:scale-105 group">
                                <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">Budget</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', function() {
            // Task completion animation
            Livewire.on('task-completed', () => {
                const toast = document.createElement('div');
                toast.className = 'fixed top-24 right-6 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-slide-in';
                toast.innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Task marked as complete!</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('animate-slide-out');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });
        });
    </script>
    <style>
        @keyframes slide-in {
            from {
                transform: translateX(120%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(120%);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .animate-slide-out {
            animation: slide-out 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
    </style>
@endpush
