<div class="space-y-8">

    <!-- Welcome -->
    <div>
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
            Welcome back, {{ $host->full_name }} 👋
        </h1>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
            Here's what's happening with your event planning
        </p>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div
            class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">

        @php
            $cards = [
                ['label' => 'Total Bookings', 'value' => $stats['total_bookings'], 'color' => 'indigo'],
                ['label' => 'Favourite Vendors', 'value' => $stats['favourite_vendors'], 'color' => 'purple'],
                ['label' => 'Guest Groups', 'value' => $stats['guest_groups'], 'color' => 'green'],
                ['label' => 'Checklist Items', 'value' => $stats['checklist_items'], 'color' => 'amber'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    {{ $card['label'] }}
                </p>
                <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ $card['value'] }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="mb-4 text-xl font-bold text-zinc-900 dark:text-white">
            Quick Actions
        </h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <a href="#"
               class="group flex items-center gap-4 rounded-lg border border-zinc-200 p-4 transition hover:border-indigo-500 hover:bg-indigo-50 dark:border-zinc-700 dark:hover:bg-indigo-900/20">
                <div class="rounded-lg bg-indigo-100 p-3 dark:bg-indigo-900/40">
                    🔍
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Browse Vendors</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Find your perfect match</p>
                </div>
            </a>

            <a href="#"
               class="group flex items-center gap-4 rounded-lg border border-zinc-200 p-4 transition hover:border-purple-500 hover:bg-purple-50 dark:border-zinc-700 dark:hover:bg-purple-900/20">
                <div class="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/40">
                    ➕
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Create Guest Group</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Organize your guests</p>
                </div>
            </a>

            <a href="#"
               class="group flex items-center gap-4 rounded-lg border border-zinc-200 p-4 transition hover:border-green-500 hover:bg-green-50 dark:border-zinc-700 dark:hover:bg-green-900/20">
                <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/40">
                    ✅
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">View Checklist</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Track your progress</p>
                </div>
            </a>

        </div>
    </div>

    <!-- Event Details -->
    @if ($host->wedding_date || $host->event_type || $host->estimated_guests)
        <div class="rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white shadow-lg">
            <h2 class="mb-4 text-2xl font-bold">Your Event Details</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                @if ($host->event_type)
                    <div>
                        <p class="text-sm opacity-80">Event Type</p>
                        <p class="font-semibold">{{ $host->event_type }}</p>
                    </div>
                @endif

                @if ($host->wedding_date)
                    <div>
                        <p class="text-sm opacity-80">Event Date</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($host->wedding_date)->format('M d, Y') }}
                        </p>
                    </div>
                @endif

                @if ($host->estimated_guests)
                    <div>
                        <p class="text-sm opacity-80">Estimated Guests</p>
                        <p class="font-semibold">
                            {{ number_format($host->estimated_guests) }}
                        </p>
                    </div>
                @endif

            </div>
        </div>
    @endif

</div>
