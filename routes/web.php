<?php

use App\Livewire\Host\HostDashboard\HostDashboard;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use \App\Livewire\Host\Auth\HostSignup;
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('host')->name('host.')->group(function () {
    // Guest Routes (Not Authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/signup', HostSignup::class)->name('host-signup');
        Route::get('/verify-otp', \App\Livewire\Host\Auth\HostVerifyOtp::class)->name('verify-otp');
        Route::get('/login', \App\Livewire\Host\Auth\HostLogin::class)->name('host-login');
        Route::get('/forgot-password', function () {
            // Create this component later
            return 'Forgot Password Page';
        })->name('forgot-password');
    });

    // Authenticated Routes
    Route::middleware('auth:host')->group(function () {
        // Dashboard
        Route::get('/dashboard', HostDashboard::class)->name('host-dashboard');

        // Vendors
        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/', \App\Livewire\Host\Vendors\Index::class)->name('index');
//            Route::get('/favourites', \App\Livewire\Host\Vendors\Favourites::class)->name('favourites');
        });

        // Bookings
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', \App\Livewire\Host\Bookings\Index::class)->name('index');
//            Route::get('/create', \App\Livewire\Host\Bookings\Create::class)->name('create');
//            Route::get('/{booking}', \App\Livewire\Host\Bookings\Show::class)->name('show');
//            Route::get('/{booking}/edit', \App\Livewire\Host\Bookings\Edit::class)->name('edit');
        });

        // Guests
        Route::prefix('guests')->name('guests.')->group(function () {
            Route::get('/', \App\Livewire\Host\Guests\Index::class)->name('index');
//            Route::get('/groups', \App\Livewire\Host\Guests\Groups::class)->name('groups');
        });

        // Checklists
        Route::prefix('checklists')->name('checklists.')->group(function () {
            Route::get('/', \App\Livewire\Host\Checklists\Personalized::class)->name('index');
            Route::get('/personalized', \App\Livewire\Host\Checklists\Personalized::class)->name('personalized');
        });



        // Logout
        Route::post('/logout', function () {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('host.host-login');
        })->name('logout');
    });
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
