<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::prefix('host')->name('host.')->group(function () {

    // Guest Routes (Not Authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/signup', \App\Livewire\Auth\HostSignup::class)->name('signup');
        Route::get('/verify-otp', \App\Livewire\Auth\HostVerifyOtp::class)->name('verify-otp');
        Route::get('/login', \App\Livewire\Auth\HostLogin::class)->name('host-login');
        Route::get('/forgot-password', function () {
            // Create this component later
            return 'Forgot Password Page';
        })->name('forgot-password');
    });

    // Authenticated Routes
    Route::middleware('auth:host')->group(function () {
        Route::get('/dashboard', \App\Livewire\Dashboard\HostDashboard::class)->name('host-dashboard');


        // Logout
        Route::post('/logout', function () {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('host.host-login');
        })->name('logout');
    });
});


//Route::get('/host/signup', \App\Livewire\Auth\HostSignup::class)->name('host.signup');
//Route::get('/host/verify', \App\Livewire\Auth\HostVerifyOtp::class)->name('host.verify');
//Route::get('/host/login', \App\Livewire\Auth\HostLogin::class)->name('host.login');
//Route::post('/host/logout', function () {
//    auth('host')->logout();
//    return redirect()->route('host.login');
//})->name('host.logout');
//
//
//
//Route::middleware('auth:host')->group(function () {
//    Route::get('/host/dashboard', \App\Livewire\Dashboard\HostDashboard::class)
//        ->name('host.dashboard');
//});


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
