<header class="sticky top-0 md:relative bg-white border-b border-zinc-200 dark:border-zinc-800 z-50" x-data="{
    mobileMenuOpen: @entangle('mobileMenuOpen'),
    showProfileMenu: @entangle('showProfileMenu'),
    mobileProfileOpen: @entangle('mobileProfileOpen'),
    searchFocused: @entangle('searchFocused'),
    categoryOpen: @entangle('categoryOpen')
}" @click.away="showProfileMenu = false; mobileProfileOpen = false; searchFocused = false; categoryOpen = false">

    {{-- Mobile Menu Overlay --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         @click="$wire.toggleMobileMenu()"></div>

    {{-- Mobile Menu Sidebar --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed top-0 left-0 h-full w-full max-w-sm bg-white dark:bg-zinc-900 shadow-lg z-50 lg:hidden">

        <div class="flex flex-col h-full">
            {{-- Mobile Menu Header --}}
            <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-800">
                <h2 class="text-lg tracking-wider font-semibold text-zinc-900 dark:text-white">MENU</h2>
                <button @click="$wire.toggleMobileMenu()" class="p-2">
                    <flux:icon.x class="h-6 w-6" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                {{-- Main Navigation --}}
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                    <nav class="space-y-3">
                        <a href="{{ route('wedding-venues') }}"
                           wire:navigate
                           class="block py-2 text-sm font-medium text-zinc-800 dark:text-zinc-200 hover:text-zinc-600 dark:hover:text-zinc-400 capitalize">
                            Venues
                        </a>
                        <a href="{{ route('wedding-vendors') }}"
                           wire:navigate
                           class="block py-2 text-sm font-medium text-zinc-800 dark:text-zinc-200 hover:text-zinc-600 dark:hover:text-zinc-400 capitalize">
                            Vendors
                        </a>
                        <a href="{{ route('wedding-planner') }}"
                           wire:navigate
                           class="block py-2 text-sm font-medium text-zinc-800 dark:text-zinc-200 hover:text-zinc-600 dark:hover:text-zinc-400 capitalize">
                            Planners
                        </a>
                    </nav>
                </div>

                {{-- Event Types --}}
                <div class="p-4">
                    <nav class="space-y-2">
                        <div>
                            <button @click="$wire.toggleMobileEventMenu('1')"
                                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-md transition-all duration-200">
                                <span>Weddings</span>
                                <flux:icon.chevron-down class="h-4 w-4 transition-transform duration-200"
                                                        ::class="{ 'rotate-180': {{ $mobileEventMenuOpen === '1' ? 'true' : 'false' }} }" />
                            </button>
                        </div>
                        <a href="{{ route('coming-soon') }}"
                           wire:navigate
                           class="block px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-md transition-all duration-200">
                            Pamper Me
                        </a>
                        <a href="{{ route('coming-soon') }}"
                           wire:navigate
                           class="block px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-md transition-all duration-200">
                            Man Down
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Bottom Actions --}}
            @if(!$host && !$vendor)
                <div class="sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-4 space-y-3">
                    <a href="{{ route('user-login') }}"
                       wire:navigate
                       class="block w-full text-center py-2.5 px-4 border border-primary-900 text-primary-900 rounded-md text-sm hover:bg-primary-50 dark:hover:bg-primary-950 transition-colors">
                        Login / Signup
                    </a>
                    <a href="{{ route('vendor-plans') }}"
                       wire:navigate
                       class="block w-full text-center py-2.5 px-4 bg-primary-900 text-white rounded-md text-sm hover:bg-primary-800 transition-colors">
                        Wedbooki Vendor ?
                    </a>
                </div>
            @else
                <div class="sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-4">
                    <button wire:click="logout"
                            class="block w-full text-center py-2.5 px-4 border border-red-600 text-red-600 rounded-md text-sm hover:bg-red-50 dark:hover:bg-red-950 transition-colors">
                        Logout
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Main Navigation --}}
    <div class="mx-auto px-2">
        <div class="flex h-16 items-center justify-between">
            {{-- Left Section --}}
            <div class="flex gap-5">
                {{-- Mobile Menu Button --}}
                <button @click="$wire.toggleMobileMenu()" class="lg:hidden" aria-label="Toggle menu">
                    <flux:icon.bars-3 class="h-7 w-7" x-show="!mobileMenuOpen" />
                    <flux:icon.x class="h-6 w-6" x-show="mobileMenuOpen" />
                </button>

                <div class="leftNav h-full items-center flex">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}"
                       wire:navigate
                       class="w-[100px] h-10 lg:w-[110px] lg:h-12 flex items-center justify-start">
                        <img src="/imgs/logo/logo1.png" class="w-full h-full" alt="Wedbooki Logo" />
                    </a>

                    {{-- Desktop Nav Links --}}
                    <nav class="hidden lg:block h-full py-3 ml-6">
                        <ul class="flex h-full items-center">
                            <li class="h-full px-4 border-r border-zinc-300 dark:border-zinc-700 flex justify-center items-center">
                                <a href="{{ route('wedding-venues') }}"
                                   wire:navigate
                                   class="text-[13px] uppercase font-medium text-black dark:text-white hover:text-zinc-600 dark:hover:text-zinc-400 transition-all duration-200">
                                    Venues
                                </a>
                            </li>
                            <li class="h-full px-4 border-r border-zinc-300 dark:border-zinc-700 flex justify-center items-center">
                                <a href="{{ route('wedding-vendors') }}"
                                   wire:navigate
                                   class="text-[13px] uppercase font-medium text-black dark:text-white hover:text-zinc-600 dark:hover:text-zinc-400 transition-all duration-200">
                                    Vendors
                                </a>
                            </li>
                            <li class="h-full px-4 border-r border-zinc-300 dark:border-zinc-700 flex justify-center items-center">
                                <a href="{{ route('wedding-planner') }}"
                                   wire:navigate
                                   class="text-[13px] uppercase font-medium text-black dark:text-white hover:text-zinc-600 dark:hover:text-zinc-400 transition-all duration-200">
                                    Planners
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            {{-- Right Section --}}
            <div class="flex items-center space-x-2 sm:space-x-4">
                {{-- Region Selector (Desktop) --}}
                <div class="hidden lg:block">
                    {{-- Add Region Selector Component Here --}}
                </div>

                {{-- Search Bar (Desktop & Tablet) --}}
                <div class="relative hidden sm:block" x-data="{ searchRef: $refs.searchContainer }">
                    <div class="flex items-center rounded-md border border-zinc-300 dark:border-zinc-700">
                        <div class="flex items-center pl-2 sm:pl-3">
                            <flux:icon.magnifying-glass class="h-4 w-4 text-zinc-400" />
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="searchValue"
                               @focus="$wire.focusSearch()"
                               placeholder='Try "Four Seasons"'
                               class="w-32 border-0 py-2 pl-1 pr-1 text-sm bg-transparent focus:outline-none focus:ring-0 sm:w-48 md:w-64 dark:text-white" />

                        <div class="hidden items-center border-l border-zinc-300 dark:border-zinc-700 px-2 sm:flex sm:px-3">
                            <button @click="$wire.toggleCategoryDropdown()"
                                    class="flex items-center space-x-1">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                    in {{ $selectedCategory }}s
                                </span>
                                <flux:icon.chevron-down class="ml-1 h-3 w-3 text-zinc-400 transition-transform"
                                                        ::class="{ 'rotate-180': categoryOpen }" />
                            </button>

                            {{-- Category Dropdown --}}
                            <div x-show="categoryOpen"
                                 x-transition
                                 class="absolute right-0 top-full z-10 mt-1 w-32 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-1 shadow-lg">
                                @foreach($searchCategories as $category)
                                    <button wire:click="selectCategory('{{ $category }}')"
                                            class="w-full px-4 py-2 text-left text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                        in {{ $category }}s
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Search Results Dropdown --}}
                    <div x-show="searchFocused && {{ count($searchResults) > 0 ? 'true' : 'false' }}"
                         x-transition
                         class="absolute left-0 right-0 top-full z-10 mt-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 shadow-lg">
                        <div class="px-4 py-1 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                            Popular {{ $selectedCategory }}s
                        </div>
                        <div class="p-4 flex flex-col gap-2">
                            @foreach($searchResults as $result)
                                <a href="{{ route('vendor.show', $result->id) }}"
                                   wire:navigate
                                   class="flex gap-2 items-center hover:bg-zinc-50 dark:hover:bg-zinc-700 p-2 rounded">
                                    <div>
                                        <img src="{{ $result->image }}"
                                             class="h-10 w-10 rounded-full object-cover"
                                             alt="{{ $result->company_name }}" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ $result->company_name }}
                                        </p>
                                        <p class="text-[0.8rem] text-zinc-400 capitalize">
                                            {{ $result->category }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Region Selector (Mobile) --}}
                <div class="lg:hidden">
                    {{-- Add Region Selector Component Here --}}
                </div>

                {{-- Mobile Profile Icon --}}
                <div class="relative lg:hidden">
                    <button @click="$wire.toggleMobileProfile()"
                            class="relative p-2 rounded-full transition-all duration-300 hover:bg-primary-50 dark:hover:bg-primary-950 active:scale-95">
                        <div class="relative">
                            @if($host?->profile_image || $vendor?->profile_image)
                                <img src="{{ $host?->profile_image ?? $vendor?->profile_image }}"
                                     alt="Profile"
                                     class="h-9 w-9 rounded-full object-cover border-2 border-white dark:border-zinc-800" />
                            @else
                                <flux:icon.user-circle class="h-7 w-7 text-primary-900" />
                            @endif
                            <span class="absolute top-0 right-0 h-2.5 w-2.5 bg-primary-900 rounded-full border-2 border-white dark:border-zinc-900"></span>
                        </div>
                    </button>

                    {{-- Mobile Profile Dropdown --}}
                    <div x-show="mobileProfileOpen"
                         x-transition
                         class="absolute right-0 top-full z-50 mt-1 w-56 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-1 shadow-lg">
                        @if(!$host && !$vendor)
                            <a href="{{ route('vendor-plans') }}"
                               wire:navigate
                               class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                Are you vendor?
                            </a>
                            <a href="{{ route('user-login') }}"
                               wire:navigate
                               class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                Login
                            </a>
                        @else
                            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $host?->full_name ?? $vendor?->full_name }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $host?->email ?? $vendor?->email }}
                                </p>
                            </div>
                            <a href="{{ $vendor ? route('vendor.dashboard') : route('host.dashboard') }}"
                               wire:navigate
                               class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                <flux:icon.squares-2x2 class="h-4 w-4" />
                                Dashboard
                            </a>
                            <a href="{{ $vendor ? route('vendor.profile') : route('host.profile') }}"
                               wire:navigate
                               class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                <flux:icon.user class="h-4 w-4" />
                                Profile
                            </a>
                            <div class="border-t border-zinc-100 dark:border-zinc-700">
                                <button wire:click="logout"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-700 w-full text-left">
                                    <flux:icon.arrow-right-start-on-rectangle class="h-4 w-4" />
                                    Logout
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Navigation (Desktop Only) --}}
    <div class="border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 relative hidden lg:block">
        <div class="mx-auto flex items-center justify-between px-8">
            <nav class="overflow-x-auto py-3">
                <ul class="flex space-x-4 whitespace-nowrap sm:space-x-8">
                    <li>
                        <a href="#"
                           class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 sm:text-sm transition-all duration-200 flex items-center gap-2">
                            Weddings
                            <flux:icon.chevron-down class="h-3 w-3" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('#') }}"
                           wire:navigate
                           class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 sm:text-sm transition-all duration-200">
                            Pamper Me
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('#') }}"
                           wire:navigate
                           class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 sm:text-sm transition-all duration-200">
                            Man Down
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                @if(!$host && !$vendor)
                    <div class="space-x-3">
                        <a href="{{ route('host.host-login') }}"
                           wire:navigate
                           class="text-[14px] text-black dark:text-white hover:text-primary-900 dark:hover:text-primary-400 capitalize transition duration-300">
                            LogIn / SignUp
                        </a>
                        <a href="{{ route('#') }}"
                           wire:navigate
                           class="text-[14px] inline-block cursor-pointer py-2 px-4 rounded-md bg-primary-900 text-white hover:bg-primary-800 transition duration-300">
                            Wedbooki Vendor
                        </a>
                    </div>
                @endif

                {{-- Desktop Profile Menu --}}
                @if($host || $vendor)
                    <div class="relative">
                        <div class="flex items-center gap-3">
                            {{-- Messages Icon --}}
                            <a href="{{ $vendor ? route('#) : route('#') }}"
                               wire:navigate
                               class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:text-primary-900 dark:hover:text-primary-400 transition-colors">
                                <flux:icon.chat-bubble-left-right class="h-5 w-5" />
                            </a>

                            {{-- Favorites (Host Only) --}}
                            @if($host)
                                <a href="{{ route('host.vendor-manager', ['filter' => 'favourites']) }}"
                                   wire:navigate
                                   class="p-2 text-red-500 transition-colors">
                                    <flux:icon.heart class="h-6 w-6" />
                                </a>
                            @endif

                            {{-- Profile Button --}}
                            <button @click="$wire.toggleProfileMenu()"
                                    class="flex items-center gap-2 px-4 py-2">
                                @if($host?->profile_image || $vendor?->profile_image)
                                    <img src="{{ $host?->profile_image ?? $vendor?->profile_image }}"
                                         class="w-10 h-10 rounded-full object-cover" />
                                @else
                                    <div class="w-10 h-10 rounded-full bg-primary-900 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($host?->full_name ?? $vendor?->full_name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <flux:icon.chevron-down class="h-4 w-4 transition-transform duration-300"
                                                        ::class="{ 'rotate-180': showProfileMenu }" />
                            </button>
                        </div>

                        {{-- Desktop Profile Dropdown --}}
                        <div x-show="showProfileMenu"
                             x-transition
                             class="absolute right-0 w-72 bg-white dark:bg-zinc-800 rounded-xl shadow-2xl py-0 z-50 border border-zinc-100 dark:border-zinc-700 overflow-hidden">

                            {{-- Profile Header --}}
                            <div class="px-5 py-4 bg-gradient-to-br from-primary-900 to-primary-700">
                                <div class="flex items-center gap-3">
                                    @if($host?->profile_image || $vendor?->profile_image)
                                        <img src="{{ $host?->profile_image ?? $vendor?->profile_image }}"
                                             class="w-12 h-12 rounded-full object-cover ring-2 ring-white/30" />
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-semibold text-lg">
                                            {{ strtoupper(substr($host?->full_name ?? $vendor?->full_name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base font-medium text-white capitalize truncate">
                                            {{ $host?->full_name ?? $vendor?->full_name }}
                                        </p>
                                        <a href="{{ $vendor ? route('vendor.profile') : route('host.profile') }}"
                                           wire:navigate
                                           class="text-xs text-white/80 hover:text-white transition-colors">
                                            Edit Profile →
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="p-3">
                                <a href="{{ $vendor ? route('vendor.dashboard') : route('host.dashboard') }}"
                                   wire:navigate
                                   class="flex items-center justify-center gap-2 w-full bg-primary-900 hover:bg-primary-800 text-white py-2.5 px-4 rounded-lg transition-all duration-200 text-sm">
                                    @if($vendor)
                                        <flux:icon.squares-2x2 class="w-4 h-4" />
                                        Go To Dashboard
                                    @else
                                        <flux:icon.sparkles class="w-4 h-4" />
                                        My Wedding
                                    @endif
                                </a>
                            </div>

                            {{-- Bottom Links --}}
                            <div class="px-3 py-3 border-t border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/50">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('settings') }}"
                                       wire:navigate
                                       class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary-900 dark:hover:text-primary-400 py-1.5 px-3 rounded-lg hover:bg-white dark:hover:bg-zinc-800 transition-all duration-200">
                                        <flux:icon.cog-6-tooth class="h-4 w-4" />
                                        Settings
                                    </a>
                                    <button wire:click="logout"
                                            class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 py-1.5 px-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-950 transition-all duration-200">
                                        <flux:icon.arrow-right-start-on-rectangle class="h-4 w-4" />
                                        Log Out
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
