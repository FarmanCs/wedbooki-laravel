<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Navbar backdrop blur effect */
        .navbar-blur {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(161, 161, 170, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(161, 161, 170, 0.5);
        }

        .dark ::-webkit-scrollbar-thumb {
            background: rgba(63, 63, 70, 0.5);
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(63, 63, 70, 0.7);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-zinc-50 via-white to-purple-50 dark:from-zinc-950 dark:via-zinc-900 dark:to-purple-950/20">

<!-- Sticky Navbar with enhanced styling -->
<nav class="sticky top-0 z-50 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/80 dark:bg-zinc-900/80 navbar-blur shadow-sm">
    <flux:navbar class="max-w-7xl mx-auto">

        <!-- Logo -->
        <a href="{{ route('host.host-dashboard') }}" class="flex items-center gap-5 px-2 hover:opacity-80 transition-opacity">
            <x-app-logo/>
        </a>

        <!-- Nav Items -->
        <flux:navbar.item
            :href="route('host.host-dashboard')"
            :current="request()->routeIs('host.host-dashboard')"
            wire:navigate
            class="relative group"
        >
            <span class="relative z-10">Home</span>
            @if(request()->routeIs('host.host-dashboard'))
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600"></span>
            @endif
        </flux:navbar.item>

        <flux:navbar.item
            :href="route('host.vendors.index')"
            :current="request()->routeIs('host.vendors.*')"
            wire:navigate
            class="relative group"
        >
            <span class="relative z-10">Vendors</span>
            @if(request()->routeIs('host.vendors.*'))
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600"></span>
            @endif
        </flux:navbar.item>

        <flux:navbar.item
            :href="route('host.bookings.index')"
            :current="request()->routeIs('host.bookings.*')"
            wire:navigate
            class="relative group"
        >
            <span class="relative z-10">Bookings</span>
            @if(request()->routeIs('host.bookings.*'))
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600"></span>
            @endif
        </flux:navbar.item>

        <flux:navbar.item
            :href="route('host.checklists.index')"
            :current="request()->routeIs('host.checklists.*')"
            wire:navigate
            class="relative group"
        >
            <span class="relative z-10">Checklist</span>
            @if(request()->routeIs('host.checklists.*'))
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600"></span>
            @endif
        </flux:navbar.item>

        <flux:navbar.item
            :href="route('host.guests.index')"
            :current="request()->routeIs('host.guests.*')"
            wire:navigate
            class="relative group"
        >
            <span class="relative z-10">Guests</span>
            @if(request()->routeIs('host.guests.*'))
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600"></span>
            @endif
        </flux:navbar.item>

{{--         PUSH SEARCH TO RIGHT--}}
        <flux:spacer/>

        <!-- Global Search with enhanced styling -->
        <div class="relative max-w-md">
            <flux:input
                icon="magnifying-glass"
                placeholder="Search..."
                wire:model.debounce.300ms="search"
                class="bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 focus:border-purple-500 dark:focus:border-purple-500 transition-colors"
            />
        </div>

        <!-- Notifications (Optional) -->
        <flux:dropdown position="bottom" align="end">
            <button class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <!-- Notification badge -->
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <flux:menu class="w-80">
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <p class="font-semibold text-zinc-900 dark:text-white">Notifications</p>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <flux:menu.item>
                        <div class="flex items-start gap-3 py-2">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">New booking confirmed</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Your booking with ABC Catering has been confirmed</p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">2 hours ago</p>
                            </div>
                        </div>
                    </flux:menu.item>
                </div>
                <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                    <a href="#" class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700">View all notifications</a>
                </div>
            </flux:menu>
        </flux:dropdown>

        <!-- User Menu with enhanced styling -->
        <flux:dropdown position="bottom" align="end">
            <div class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                <flux:profile
                    :full_name="auth()->user()->full_name"
                    :initials="auth()->user()->initials()"
                    class="ring-2 ring-purple-500/20"
                />
            </div>

            <flux:menu class="w-64">

                <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                    <p class="font-semibold text-zinc-900 dark:text-white">
                        {{ auth()->user()->full_name }}
                    </p>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ auth()->user()->email }}
                    </p>
                    @if(auth()->user()->wedding_date)
                        <div class="mt-2 inline-flex items-center px-2 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-xs font-medium text-purple-700 dark:text-purple-300">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse(auth()->user()->wedding_date)->format('M d, Y') }}
                        </div>
                    @endif
                </div>

{{--                <flux:menu.separator/>--}}

                <flux:menu.item
                    icon="user"
                    :href="route('profile.edit')"
                    wire:navigate
                >
                    My Profile
                </flux:menu.item>

                <flux:menu.item
                    icon="cog"
                    :href="route('profile.edit')"
                    wire:navigate
                >
                    Settings
                </flux:menu.item>

                <flux:menu.item icon="heart">
                    My Favorites
                </flux:menu.item>

                <flux:menu.separator/>

                <flux:menu.item icon="question-mark-circle">
                    Help & Support
                </flux:menu.item>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full text-red-600 dark:text-red-400"
                    >
                        Log Out
                    </flux:menu.item>
                </form>

            </flux:menu>
        </flux:dropdown>

    </flux:navbar>
</nav>

<!-- Page Content with proper spacing from navbar -->
<main>
    {{ $slot }}
</main>

<!-- Footer (Optional) -->
<footer class="mt-16 border-t border-zinc-200 dark:border-zinc-800 bg-white/50 dark:bg-zinc-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-app-logo class="h-6"/>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">© 2025 All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-zinc-600 dark:text-zinc-400">
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Contact</a>
            </div>
        </div>
    </div>
</footer>

@fluxScripts
</body>
</html>
