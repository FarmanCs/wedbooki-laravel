<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Host\AuthController;
use App\Http\Controllers\Host\ProfileController;
use App\Http\Controllers\Host\BookingController;
use App\Http\Controllers\Host\ReviewController;
use App\Http\Controllers\Host\ChecklistController;
use App\Http\Controllers\Host\GuestGroupController;
use App\Http\Controllers\Host\FavouriteController;
use App\Http\Controllers\Host\GoogleCalendarController;
use App\Http\Controllers\Host\AccountController;
use App\Http\Controllers\Vendor\VendorController;

//use App\Http\Controllers\Host\SessionController;

Route::prefix('/v1/host')->group(function () {

    // AUTHENTICATION
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/host-verify-signup', [AuthController::class, 'verifySignup']);
    Route::get('/resend-signup-otp', [AuthController::class, 'resendSignupOtp']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forget-password', [AuthController::class, 'forgetPassword']);

//need authentication
    Route::group(['prefix' => '/', 'middleware' => ['auth:sanctum']], function () {
        Route::post('/verify-otp', [AuthController::class, 'hostVerifyOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::patch('/update-password/{id?}', [AuthController::class, 'hostUpdatePassword']);

        Route::post('/password-change-request/{id}', [AuthController::class, 'passwordChangeRequest']);
        Route::post('/password-change-verify/{id}', [AuthController::class, 'passwordChangeVerify']);

        // PROFILE
        Route::put('/update-profile', [ProfileController::class, 'updateProfile']);
        Route::get('/profile', [ProfileController::class, 'getProfile']);

        // BUDGET
        Route::patch('/update-budget/{id?}', [ProfileController::class, 'updateBudget']);
        Route::get('/get-budget/{id?}', [ProfileController::class, 'getBudget']);

        // Check List
        Route::put('/add-checklist-template/{id?}', [ChecklistController::class, 'createTemplate']);
        Route::get('/get-all-templates', [ChecklistController::class, 'getAllTemplates']);


        // EMAIL CHANGE
        Route::put('/change-email/{id}', [ProfileController::class, 'changeEmail']);

        // WEDDING DATES
        Route::put('/add-wedding-date/{id?}', [ProfileController::class, 'addWeddingDate']);
        Route::delete('/delete-wedding-date/{id?}', [ProfileController::class, 'deleteWeddingDate']);
        Route::get('/get-wedding-date/{id?}', [ProfileController::class, 'getWeddingDate']);


        // REVIEWS working on these
        Route::post('/give-review/{id?}', [ReviewController::class, 'giveReview']);
        Route::patch('/update-review/{id?}', [ReviewController::class, 'editReview']);
        Route::delete('/delete-review/{id?}', [ReviewController::class, 'deleteReview']);
        Route::get('/get-vendor-reviews/{id?}', [ReviewController::class, 'getAllVendorReviews']);

        // GUEST GROUPS
        Route::post('/create-guest-group/{id?}', [GuestGroupController::class, 'createGuestGroup']);
        Route::post('/add-guest-to-group/{id?}', [GuestGroupController::class, 'addGuestsToGroup']);
        Route::patch('/update-guest/{id?}', [GuestGroupController::class, 'updateGuest']);
        Route::get('/update-guest-status/{id?}', [GuestGroupController::class, 'rsvpGuest']);
        Route::get('/all-my-groups/{id?}', [GuestGroupController::class, 'getMyGroups']);
        Route::get('/get-all-groups', [GuestGroupController::class, 'getAllGroups']);

        // NEW ADDITIONAL ROUTES FROM EXPRESS
        Route::delete('/delete-guest/{id?}', [GuestGroupController::class, 'deleteGuest']);
        Route::post('/add-guest/{id?}', [GuestGroupController::class, 'addGuest']);
        Route::delete('/delete-group/{id}', [GuestGroupController::class, 'deleteGroup']);

        // FAVOURITES
        Route::post('/add-to-favourite', [FavouriteController::class, 'addFavourite']);
        Route::get('/get-all-favourites', [FavouriteController::class, 'getFavouritesByHost']);

        // VENDOR TIMINGS
        Route::get('/vendor-timings/{id}', [BookingController::class, 'vendorTimings']);

        // ---------------------------
        // CHECKLIST
        // ---------------------------

        Route::put('/assign-checklist/{id}', [ChecklistController::class, 'assignChecklist']);

        Route::put('/checklist/toggle/{hostId}', [ChecklistController::class, 'toggleChecklistStatus']);
        Route::delete('/delete-checklist-item/{hostId}', [ChecklistController::class, 'deleteChecklistItem']);
        Route::post('/add-custom-checklist-item/{hostId}', [ChecklistController::class, 'addCustomChecklistItem']);
        Route::put('/update-checklist-item/{hostId}/checklist/{itemId}', [ChecklistController::class, 'editChecklistItem']);

        // ---------------------------
        // ACCOUNT (DEACTIVATE / DELETE)
        // ---------------------------
        Route::post('/deactivate-request/{id}', [AccountController::class, 'deactivateRequest']);
        Route::post('/deactivate-verify/{id}', [AccountController::class, 'deactivateVerify']);
        Route::post('/delete-request/{id}', [AccountController::class, 'deleteRequest']);
        Route::post('/delete-verify/{id}', [AccountController::class, 'deleteVerify']);
        Route::delete('/delete-account/{id}', [AccountController::class, 'deleteAccount']);

        // ---------------------------
        // GOOGLE CALENDAR
        // ---------------------------
        Route::post('/google-calendar/save-tokens', [GoogleCalendarController::class, 'saveTokens']);
        Route::post('/google-calendar/create-event', [GoogleCalendarController::class, 'createEvent']);
        Route::get('/google-calendar/status/{id}', [GoogleCalendarController::class, 'getStatus']);
        Route::post('/unlink-google-account', [GoogleCalendarController::class, 'unlink']);
    });
    // ---------------------------
    // SESSIONS (RECENT ACTIVITY)
    // ---------------------------
//    Route::get('/sessions', [SessionController::class, 'getAllSessions']);
//    Route::get('/session/{hostId}', [SessionController::class, 'getSessionByHostId']);
//    Route::patch('/cancel-session/{hostId}', [SessionController::class, 'cancelSession']);

    //pending routes for the moments
    Route::post('/google-auth', [AuthController::class, 'googleLogin']);
    Route::post('/apple-auth', [AuthController::class, 'appleLogin']);

    Route::put('/email-change-otp/{id}', [ProfileController::class, 'verifyChangeEmailOtp']);


    // ---------------------------
    // BOOKINGS
    // ---------------------------
    Route::post('/book-venue/{id?}', [BookingController::class, 'createVenueBooking']);


    //pending routes . . .  all booking
    Route::put('/reject-venue-booking/{bookingId}', [BookingController::class, 'rejectVenueBooking']);
    Route::put('/cancel-venue-booking/{bookingId}', [BookingController::class, 'cancelVenueBooking']);

    Route::post('/book-vendor/{id}', [BookingController::class, 'createVendorBooking']);
    Route::put('/cancel-booking/{id}', [BookingController::class, 'cancelBooking']);

    Route::get('/my-bookings/{id?}', [BookingController::class, 'getAllBookings']);
    Route::get('/getbooking/{bookingId}', [BookingController::class, 'getBookingById']);
    Route::get('/host-booking-detail/{id}', [BookingController::class, 'hostBookingDetail']);

    Route::patch('/get-booked-vendors', [BookingController::class, 'getBookedVendors']);

});

//vendors routes goes here
// Public routes (no authentication required)
Route::prefix('/v1/vendor')->group(function () {
    Route::post('/signup', [VendorController::class, 'signup']);
    Route::post('/verify-signup', [VendorController::class, 'verifySignup']);
    Route::post('/google-auth', [VendorController::class, 'googleAuth']);
    Route::post('/apple-auth', [VendorController::class, 'appleAuth']);
    Route::post('/login', [VendorController::class, 'login']);
    Route::post('/forget-password', [VendorController::class, 'forgetPassword']);
    Route::post('/verify-otp', [VendorController::class, 'verifyOtp']);
    Route::post('/resend-otp', [VendorController::class, 'resendOtp']);
    Route::post('/reset-password', [VendorController::class, 'resetPassword']);
    Route::post('/reactivate-verify', [VendorController::class, 'reactivateVerify']);

    // Email change verification (open endpoint)
    Route::put('/email-change-otp/{id}', [VendorController::class, 'verifyChangeEmailOtp']);

    // Password change verification (open endpoint)
    Route::post('/password-change-verify/{id}', [VendorController::class, 'passwordChangeVerify']);

    // Deactivate/Delete verification (open endpoints)
    Route::post('/deactivate-verify/{id}', [VendorController::class, 'deactivateVerify']);
    Route::post('/delete-verify/{id}', [VendorController::class, 'deleteVerify']);

    // Public slots endpoints
    Route::get('/get-slots/{vendorId}/slots', [VendorController::class, 'getSlotsForDate']);
    Route::get('/get-vendor-available-slots/{vendorId}', [VendorController::class, 'getVendorAvailableSlots']);

    // Accept booking (no auth required based on original)
    Route::put('/accept-booking/{id}', [VendorController::class, 'acceptBooking']);
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'type.vendor'])->prefix('vendor')->group(function () {
    // Profile Management
    Route::put('/complete-profile', [VendorController::class, 'completeProfile']);
    Route::put('/update-vendor-profile/{id}', [VendorController::class, 'updateVendorProfile']);
    Route::get('/getvendor-personal-profile/{id}', [VendorController::class, 'getVendorPersonalProfile']);
    Route::get('/get-business-profile/{id}', [VendorController::class, 'vendorBusinessProfile']);
    Route::put('/update-business-profile/{id}', [VendorController::class, 'updateVendorBusinessProfile']);

    // Password & Email Management
    Route::put('/vendor-update-password/{id}', [VendorController::class, 'updatePassword']);
    Route::put('/change-email/{id}', [VendorController::class, 'changeEmail']);
    Route::post('/password-change-request/{id}', [VendorController::class, 'passwordChangeRequest']);

    // Account Management
    Route::post('/deactivate-request/{id}', [VendorController::class, 'deactivateRequest']);
    Route::post('/delete-request/{id}', [VendorController::class, 'deleteRequest']);
    Route::delete('/delete-account/{id}', [VendorController::class, 'deleteVendorAndData']);

    // Timings Management
    Route::put('/update-timings/{id}', [VendorController::class, 'updateVendorTimings']);
    Route::get('/service-timings/{id}', [VendorController::class, 'getServiceVendorTimings']);
    Route::get('/venue-timings/{id}', [VendorController::class, 'getVenueVendorTimings']);

    // Media Management
    Route::put('/update-images/{id}', [VendorController::class, 'updateVendorPortfolioImages']);
    Route::delete('/delete-portfolio-image/{id}', [VendorController::class, 'deleteVendorPortfolioImage']);
    Route::put('/update-videos/{id}', [VendorController::class, 'updateVendorVideos']);
    Route::delete('/delete-video/{id}', [VendorController::class, 'deleteVendorVideo']);

    // Unavailable Dates Management
    Route::post('/add-unavailable-date/{id}', [VendorController::class, 'addUnavailableDate']);
    Route::put('/remove-unavailable-date/{id}', [VendorController::class, 'makeDateAvailable']);
    Route::get('/get-unavailable-dates/{id}', [VendorController::class, 'getUnavailableDates']);
    Route::delete('/delete-unavailable-date', [VendorController::class, 'deleteUnavailableDate']);
    Route::put('/update-unavailable-dates', [VendorController::class, 'updateUnavailableDates']);

    // Bookings Management
    Route::get('/vendor-bookings/{id}', [VendorController::class, 'getVendorBookings']);
    Route::get('/vendor-booking-detail/{id}', [VendorController::class, 'vendorSingleBooking']);
    Route::put('/reject-booking/{id}', [VendorController::class, 'rejectBooking']);

    // Package Management
    Route::post('/create-package/{id}', [VendorController::class, 'createPackage']);
    Route::put('/update-package/{id}', [VendorController::class, 'updatePackage']);
    Route::delete('/delete-package/{id}', [VendorController::class, 'deletePackage']);
    Route::get('/all-packages/{id}', [VendorController::class, 'getAllPackages']);

    // Service Management
    Route::post('/create-service/{id}', [VendorController::class, 'createService']);
    Route::put('/update-service/{id}', [VendorController::class, 'updateService']);
    Route::delete('/delete-service/{id}', [VendorController::class, 'deleteService']);

    // Reviews Management
    Route::get('/get-all-reviews/{id}', [VendorController::class, 'getAllMyReviews']);
    Route::put('/reply-review/{id}', [VendorController::class, 'replyToReview']);
    Route::delete('/delete-reply/{id}', [VendorController::class, 'deleteReply']);
    Route::put('/update-reply/{id}', [VendorController::class, 'updateReply']);

    // Statistics
    Route::get('/total-stats/{id}', [VendorController::class, 'totalStats']);
    Route::get('/top-performing-months/{id}', [VendorController::class, 'topPerformingMonths']);
    Route::get('/packages-performance/{id}', [VendorController::class, 'getPackagePerformance']);
});
