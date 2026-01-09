<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <!-- Add Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="app-body">

<!-- Navbar matching screenshot style -->
<nav class="app-navbar navbar-blur" id="mainNavbar"
     x-data="{ mobileMenuOpen: false, profileMenuOpen: false, notificationsOpen: false }">
    <div class="navbar-container">

        <!-- Logo -->
        <a href="{{ route('host.host-dashboard') }}" class="navbar-logo">
            <x-app-logo/>
        </a>

        <!-- Desktop Navigation Items -->
        <div class="hidden lg:flex navbar-nav">
            <a
                href="{{ route('host.host-dashboard') }}"
                class="navbar-item {{ request()->routeIs('host.host-dashboard') ? 'navbar-item-active' : '' }}"
                wire:navigate
            >
                Home
            </a>

            <a
                href="{{ route('host.vendors.index') }}"
                class="navbar-item {{ request()->routeIs('host.vendors.*') ? 'navbar-item-active' : '' }}"
                wire:navigate
            >
                Vendors
            </a>

            <a
                href="{{ route('host.vendors.index') }}"
                class="navbar-item {{ request()->routeIs('host.vendors.*') ? 'navbar-item-active' : '' }}"
                wire:navigate
            >
                Venues
            </a>

            <a
                href="{{ route('host.bookings.index') }}"
                class="navbar-item {{ request()->routeIs('host.bookings.*') ? 'navbar-item-active' : '' }}"
                wire:navigate
            >
                Planners
            </a>
        </div>

        <!-- Right Section: Search & Profile -->
        <div class="navbar-actions">

            <!-- Desktop Search Bar -->
            <div class="hidden lg:block navbar-search">
                <svg class="navbar-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    placeholder='Try "Four Seasons"'
                    wire:model.debounce.300ms="search"
                    class="navbar-search-input"
                />
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button
                    @click="notificationsOpen = !notificationsOpen"
                    @click.away="notificationsOpen = false"
                    class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- Notifications Dropdown -->
                <div
                    x-show="notificationsOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-50"
                    style="display: none;"
                >
                    <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                        <p class="font-semibold text-zinc-900 dark:text-white">Notifications</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div
                            class="flex items-start gap-3 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 bg-purple-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">New booking confirmed</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Your booking with ABC Catering
                                    has been confirmed</p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                        <a href="#"
                           class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700">View
                            all notifications</a>
                    </div>
                </div>
            </div>

            <!-- Heart Icon -->
                        <button class="hidden lg:block p-2 text-zinc-600 dark:text-zinc-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

            <!-- User Profile with Custom Dropdown -->
            <div class="relative">
                <button
                    @click="profileMenuOpen = !profileMenuOpen"
                    @click.away="profileMenuOpen = false"
                    class="navbar-profile"
                >
                    <div class="navbar-profile-avatar">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="navbar-profile-dropdown">
                        <span class="navbar-profile-name">{{ auth()->user()->full_name }}</span>
                        <svg class="navbar-dropdown-icon" :class="{ 'rotate-180': profileMenuOpen }" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <!-- Profile Dropdown Menu -->
                <div
                    x-show="profileMenuOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="navbar-profile-menu absolute right-0"
                    style="display: none;"
                >
                    <!-- Profile Header -->
                    <div class="navbar-profile-menu-header">
                        <p class="font-semibold text-zinc-900 dark:text-white">
                            {{ auth()->user()->full_name }}
                        </p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                            {{ auth()->user()->email }}
                        </p>
                        @if(auth()->user()->wedding_date)
                            <div
                                class="mt-2 inline-flex items-center px-2 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-xs font-medium text-purple-700 dark:text-purple-300">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse(auth()->user()->wedding_date)->format('M d, Y') }}
                            </div>
                        @endif
                    </div>

                    <!-- Menu Items -->
                    <a
                        href="{{ route('profile.edit') }}"
                        wire:navigate
                        class="navbar-profile-menu-item"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>My Profile</span>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        wire:navigate
                        class="navbar-profile-menu-item"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Settings</span>
                    </a>

                    <button class="navbar-profile-menu-item w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>My Favorites</span>
                    </button>

                    <div class="navbar-profile-menu-separator"></div>

                    <button class="navbar-profile-menu-item w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Help & Support</span>
                    </button>

                    <div class="navbar-profile-menu-separator"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="navbar-profile-menu-logout w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden navbar-mobile-toggle"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" style="display: none;"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900"
        style="display: none;"
    >
        <div class="px-4 py-3 space-y-1">
            <!-- Mobile Search Bar -->
            <div class="mb-4 navbar-search">
                <svg class="navbar-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    placeholder='Try "Four Seasons"'
                    wire:model.debounce.300ms="search"
                    class="w-full navbar-search-input"
                />
            </div>

            <!-- Mobile Navigation Items -->
            <a
                href="{{ route('host.host-dashboard') }}"
                class="mobile-nav-item {{ request()->routeIs('host.host-dashboard') ? 'mobile-nav-item-active' : '' }}"
                wire:navigate
                @click="mobileMenuOpen = false"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>

            <a
                href="{{ route('host.vendors.index') }}"
                class="mobile-nav-item {{ request()->routeIs('host.vendors.*') ? 'mobile-nav-item-active' : '' }}"
                wire:navigate
                @click="mobileMenuOpen = false"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Vendors
            </a>

            <a
                href="{{ route('host.bookings.index') }}"
                class="mobile-nav-item {{ request()->routeIs('host.bookings.*') ? 'mobile-nav-item-active' : '' }}"
                wire:navigate
                @click="mobileMenuOpen = false"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Planners
            </a>

            <!-- Mobile Profile Menu Items -->
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <a
                    href="{{ route('profile.edit') }}"
                    class="mobile-nav-item"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Profile
                </a>

                <a
                    href="#"
                    class="mobile-nav-item"
                    @click="mobileMenuOpen = false"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    My Favorites
                </a>

                <a
                    href="#"
                    class="mobile-nav-item"
                    @click="mobileMenuOpen = false"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Help & Support
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="mobile-nav-logout w-full"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Page Content -->
<main class="px-4 sm:px-6 lg:px-8 py-6">
    {{ $slot }}
</main>

<!-- Footer -->
<footer class="mt-16 border-t border-zinc-200 dark:border-zinc-800 bg-white/50 dark:bg-zinc-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-app-logo class="h-6"/>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">© 2025 All rights reserved.</span>
            </div>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-sm text-zinc-600 dark:text-zinc-400">
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Privacy
                    Policy</a>
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Terms of
                    Service</a>
                <a href="#" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Contact</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll Effect Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.getElementById('mainNavbar');
        let lastScroll = 0;

        window.addEventListener('scroll', function () {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }

            lastScroll = currentScroll;
        });
    });
</script>

@fluxScripts
</body>
</html>
