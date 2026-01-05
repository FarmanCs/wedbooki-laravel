<div class="min-h-screen bg-zinc-50 dark:bg-zinc-800 p-6">
    <div class="mx-auto max-w-7xl space-y-8">

        <!-- Welcome Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
{{--                    @dd($host->full_name)--}}
                    Welcome back, {{ $host->full_name }} 👋
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                    Here's what's happening with your event planning
                </p>
            </div>

            <!-- User Avatar (matching screenshot) -->
            <div class="flex items-center gap-3 rounded-full bg-emerald-500 px-4 py-2 text-white">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-500 font-bold">
{{--                    @dd(inials)--}}
{{--                    @dd(auth()->user()->initials())--}}
                    {{ $host->initials() }}

                </div>
                <span class="font-semibold">{{ $host->full_name }}</span>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Cards Grid (matching screenshot layout) -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <!-- Event Countdown Card -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-2 text-xl font-bold text-zinc-900 dark:text-white">
                    Event Countdown
                </h2>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">
                    Time until your special day
                </p>

                <div class="mb-6 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/40">
                        <svg class="h-12 w-12 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                @if ($host->wedding_date)
                    @php
                        $daysUntil = \Carbon\Carbon::parse($host->wedding_date)->diffInDays(now(), false);
                        $isPast = $daysUntil < 0;
                        $daysUntil = abs($daysUntil);
                    @endphp

                    <div class="mb-6 text-center">
                        <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $daysUntil }}
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $isPast ? 'days since' : 'days to go' }}
                        </p>
                        <p class="mt-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ \Carbon\Carbon::parse($host->wedding_date)->format('F d, Y') }}
                        </p>
                    </div>
                @else
                    <div class="mb-6 text-center">
                        <p class="mb-2 text-zinc-500 dark:text-zinc-400">No date set yet</p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">Set your wedding date to see countdown</p>
                    </div>
                @endif

                <button class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">
                    {{ $host->wedding_date ? 'Update Date' : 'Set Date' }}
                </button>
            </div>

            <!-- Guest List Card -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-2 text-xl font-bold text-zinc-900 dark:text-white">
                    Guest List
                </h2>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">
                    Keep track of all your guests
                </p>

                <div class="mb-6 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/40">
                        <svg class="h-12 w-12 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>

                <div class="mb-6 text-center">
                    <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $stats['guest_groups'] ?? 0 }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        guest groups
                    </p>
                    @if ($host->estimated_guests)
                        <p class="mt-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ number_format($host->estimated_guests) }} estimated guests
                        </p>
                    @endif
                </div>

                <button class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">
                    Manage Guests
                </button>
            </div>

            <!-- Budget Card -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-2 text-xl font-bold text-zinc-900 dark:text-white">
                    Budget
                </h2>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">
                    Track your spending
                </p>

                <div class="mb-6 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/40">
                        <svg class="h-12 w-12 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>

                <div class="mb-6 text-center">
                    @if ($host->event_budget)
                        <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                            ${{ number_format($host->event_budget, 0) }}
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            total budget
                        </p>
                    @else
                        <p class="text-2xl font-bold text-zinc-400 dark:text-zinc-500">
                            Not set
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Set your budget to track spending
                        </p>
                    @endif
                </div>

                <button class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">
                    View Budget
                </button>
            </div>
        </div>

        <!-- Progress Tracking Section -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">
                Planning Progress
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <!-- Vendors Progress -->
                <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Vendors</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                                {{ $stats['favourite_vendors'] ?? 0 }} <span class="text-sm font-normal text-zinc-500">/ 18</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-zinc-400">
                        {{ $stats['favourite_vendors'] > 0 ? round(($stats['favourite_vendors'] / 18) * 100) : 0 }}%
                    </div>
                </div>

                <!-- Tasks Progress -->
                <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tasks</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                                {{ $stats['checklist_items'] ?? 0 }} <span class="text-sm font-normal text-zinc-500">items</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-zinc-400">
                        0%
                    </div>
                </div>

                <!-- Guests Progress -->
                <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Guests</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                                {{ $stats['guest_groups'] ?? 0 }} <span class="text-sm font-normal text-zinc-500">/ {{ $host->estimated_guests ?? 100 }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-zinc-400">
                        {{ $host->estimated_guests > 0 ? round(($stats['guest_groups'] / $host->estimated_guests) * 100) : 0 }}%
                    </div>
                </div>

            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">
                Quick Actions
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <a href="#" class="group flex items-center gap-4 rounded-xl border-2 border-zinc-200 bg-white p-5 transition hover:border-emerald-500 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-500">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-2xl dark:bg-indigo-900/40">
                        🔍
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">Browse Vendors</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Find your perfect match</p>
                    </div>
                </a>

                <a href="#" class="group flex items-center gap-4 rounded-xl border-2 border-zinc-200 bg-white p-5 transition hover:border-emerald-500 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-500">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-2xl dark:bg-purple-900/40">
                        ➕
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">Add Guest Group</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Organize your guests</p>
                    </div>
                </a>

                <a href="#" class="group flex items-center gap-4 rounded-xl border-2 border-zinc-200 bg-white p-5 transition hover:border-emerald-500 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-500">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-2xl dark:bg-green-900/40">
                        ✅
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">View Checklist</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Track your progress</p>
                    </div>
                </a>

                <a href="#" class="group flex items-center gap-4 rounded-xl border-2 border-zinc-200 bg-white p-5 transition hover:border-emerald-500 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-500">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-2xl dark:bg-amber-900/40">
                        📊
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">View Budget</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage expenses</p>
                    </div>
                </a>

            </div>
        </div>

        <!-- Event Details Banner -->
        @if ($host->wedding_date || $host->event_type || $host->estimated_guests)
            <div class="rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 p-8 text-white shadow-lg">
                <h2 class="mb-6 text-2xl font-bold">Your Event Details</h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @if ($host->event_type)
                        <div>
                            <p class="mb-1 text-sm opacity-80">Event Type</p>
                            <p class="text-xl font-semibold">{{ ucfirst($host->event_type) }}</p>
                        </div>
                    @endif

                    @if ($host->wedding_date)
                        <div>
                            <p class="mb-1 text-sm opacity-80">Event Date</p>
                            <p class="text-xl font-semibold">
                                {{ \Carbon\Carbon::parse($host->wedding_date)->format('F d, Y') }}
                            </p>
                        </div>
                    @endif

                    @if ($host->estimated_guests)
                        <div>
                            <p class="mb-1 text-sm opacity-80">Estimated Guests</p>
                            <p class="text-xl font-semibold">
                                {{ number_format($host->estimated_guests) }} people
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
