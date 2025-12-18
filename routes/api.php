<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Host\AuthController;
use \App\Http\Controllers\Global\GlobalController;
use App\Http\Controllers\Host\ProfileController;
use App\Http\Controllers\Host\BookingController;
use App\Http\Controllers\Host\ReviewController;
use App\Http\Controllers\Host\ChecklistController;
use App\Http\Controllers\Host\GuestGroupController;
use App\Http\Controllers\Host\FavouriteController;
use App\Http\Controllers\Host\GoogleCalendarController;
use App\Http\Controllers\Host\AccountController;
use App\Http\Controllers\Vendor\VendorController;

use App\Http\Controllers\Admin\AdminController;

//use App\Http\Controllers\Host\SessionController;
Route::prefix('/v1/host')->group(function () {

    // AUTHENTICATION
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/host-verify-signup', [AuthController::class, 'verifySignup']);
    Route::post('/resend-signup-otp', [AuthController::class, 'resendSignupOtp']);

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
        Route::post('/update-profile', [ProfileController::class, 'updateProfile']);
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

        Route::delete('/delete-guest/{id?}', [GuestGroupController::class, 'deleteGuest']);
        Route::post('/add-guest/{id?}', [GuestGroupController::class, 'addGuest']);
        Route::delete('/delete-group/{id}', [GuestGroupController::class, 'deleteGroup']);

        // FAVOURITES
        Route::post('/add-to-favourite', [FavouriteController::class, 'addFavourite']);
        Route::get('/get-all-favourites', [FavouriteController::class, 'getFavouritesByHost']);

        // VENDOR TIMINGS
        Route::get('/vendor-timings/{id}', [BookingController::class, 'vendorTimings']);

        // CHECKLIST
        Route::put('/assign-checklist/{id?}', [ChecklistController::class, 'assignChecklist']);

        Route::patch('/checklist/toggle/{hostId?}', [ChecklistController::class, 'toggleChecklistStatus']);
        Route::delete('/delete-checklist-item/{hostId?}', [ChecklistController::class, 'deleteChecklistItem']);
        Route::post('/add-custom-checklist-item/{hostId?}', [ChecklistController::class, 'addCustomChecklistItem']);
        Route::patch('/update-checklist-item/checklist/{itemId?}', [ChecklistController::class, 'editChecklistItem']);

        // ACCOUNT (DEACTIVATE / DELETE)
        Route::post('/deactivate-request/{id?}', [AccountController::class, 'deactivateRequest']);
        Route::post('/deactivate-verify/{id?}', [AccountController::class, 'deactivateVerify']);
        Route::post('/delete-request/{id?}', [AccountController::class, 'deleteRequest']);
        Route::post('/delete-verify/{id?}', [AccountController::class, 'deleteVerify']);


        // ---------------------------
        // GOOGLE CALENDAR
        // ---------------------------
        Route::post('/google-calendar/save-tokens', [GoogleCalendarController::class, 'saveHostGoogleTokens']);
        Route::post('/google-calendar/create-event', [GoogleCalendarController::class, 'createHostGoogleCalendarEvent']);
        Route::get('/google-calendar/status/{id}', [GoogleCalendarController::class, 'getHostGoogleCalendarStatus']);
        Route::post('/unlink-google-account', [GoogleCalendarController::class, 'unlinkHostGoogleAccount']);

        //bookings
        Route::post('/book-venue/{id?}', [BookingController::class, 'createVenueBooking']);

        Route::post('/book-vendor/{id?}', [BookingController::class, 'createBooking']);


        //pending routes
        Route::put('/reject-venue-booking/{bookingId}', [BookingController::class, 'rejectVenueBooking']);
        Route::put('/cancel-venue-booking/{bookingId}', [BookingController::class, 'cancelVenueBooking']);
        Route::put('/cancel-booking/{id}', [BookingController::class, 'cancelBooking']);

        Route::get('/my-bookings/{id?}', [BookingController::class, 'getAllBookings']);
        Route::get('/getbooking/{bookingId}', [BookingController::class, 'getBookingById']);
        Route::get('/host-booking-detail/{id}', [BookingController::class, 'hostBookingDetail']);

        Route::patch('/get-booked-vendors', [BookingController::class, 'getBookedVendors']);

    });
    //pending routes for the moments
    Route::post('/google-auth', [AuthController::class, 'googleLogin']);
    Route::post('/apple-auth', [AuthController::class, 'appleLogin']);
    Route::put('/email-change-otp/{id}', [ProfileController::class, 'verifyChangeEmailOtp']);

    Route::delete('/delete-account/{id?}', [AccountController::class, 'deleteAccount']);


});

//vendors routes goes here
Route::prefix('v1/vendor')->group(function () {
    // Public Routes
    Route::post('/signup', [VendorController::class, 'VendorSignup']);
    Route::post('/verify-signup-otp', [VendorController::class, 'verifySignup']);
    Route::post('/login', [VendorController::class, 'VendorLogin']);
    Route::post('/forget-password', [VendorController::class, 'VendorForgetPassword']);
    Route::post('/verify-forgot-password-otp/{id}', [VendorController::class, 'VendorForgetPasswordVerify']);
    Route::post('/resend-otp', [VendorController::class, 'VendorResendOtp']);

    // Protected Routes - Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/reset-password', [VendorController::class, 'VendorResetPassword']);
        Route::post('/vendor-update-password', [VendorController::class, 'VendorUpdatePassword']);

        Route::post('/change-email', [VendorController::class, 'VendorChangeEmail']);
        Route::patch('/email-change-otp', [VendorController::class, 'VendorVerifyChangeEmailOtp']);

        Route::get('/password-change-request', [VendorController::class, 'VendorPasswordChangeRequest']);
        Route::patch('/password-change-verify', [VendorController::class, 'VendorPasswordChangeVerify']);

        Route::get('/deactivate-request', [VendorController::class, 'VendorDeactivateRequest']);
        Route::patch('/deactivate-verify', [VendorController::class, 'VendorDeactivateVerify']);
        Route::get('/delete-request', [VendorController::class, 'VendorDeleteRequest']);
        Route::post('/delete-verify', [VendorController::class, 'VendorDeleteVerify']);

        Route::get('/get-business-profile', [VendorController::class, 'vendorBusinessProfile']);
        Route::post('/update-vendor-profile', [VendorController::class, 'VendorUpdateProfile']);
        Route::get('/get-vendor-personal-profile', [VendorController::class, 'getVendorPersonalProfile']);
        Route::post('/update-business-profile/{id}', [VendorController::class, 'updateVendorBusinessProfile']);
        Route::post('/update-timings/{id}', [VendorController::class, 'updateVendorTimings']);
        Route::get('/get-service-timings/{id}', [VendorController::class, 'GetServiceVendorTimings']);
        Route::get('/get-venue-timings/{id}', [VendorController::class, 'GetVendorVenuTimings']);

        Route::delete('/delete-portfolio-image/{id}', [VendorController::class, 'DeleteVendorPortfolioImage']);
        Route::post('/update-images/{id}', [VendorController::class, 'UpdateVendorPortfolioImages']);


        // Unavailable Dates
        Route::post('/add-unavailable-date/{businessid}', [VendorController::class, 'AddUnavailableDate']);
        Route::post('/remove-unavailable-date/{businessid}', [VendorController::class, 'MakeDateAvailable']);
        Route::get('/get-unavailable-dates/{businessid}', [VendorController::class, 'GetUnavailableDates']);
        Route::delete('/delete-unavailable-date', [VendorController::class, 'DeleteUnavailableDate']);
        Route::post('/update-unavialable-dates', [VendorController::class, 'UpdateUnavailableDates']);

        // Bookings
        Route::get('/vendor-all-bookings/{id}', [VendorController::class, 'GetVendorBookings']);
        Route::get('/vendor-booking-detail/{id}', [VendorController::class, 'VendorSingleBooking']);
        Route::post('/accept-booking/{id}', [VendorController::class, 'AcceptBooking']);
        Route::put('/reject-booking/{id}', [VendorController::class, 'RejectBooking']);

        // Packages & Services
        Route::post('/create-package/{business_id}', [VendorController::class, 'CreatePackage']);
        Route::post('/update-package/{business_id}', [VendorController::class, 'UpdatePackage']);
        Route::delete('/delete-package/{business_id}', [VendorController::class, 'DeletePackage']);
        Route::get('/all-packages/{business_id}', [VendorController::class, 'GetAllPackages']);

        Route::post('/create-service', [VendorController::class, 'CreateService']);
        Route::post('/update-service', [VendorController::class, 'UpdateService']);

        // Reviews
        Route::get('/get-all-reviews/{id}', [VendorController::class, 'GetAllMyReviews']);
        Route::put('/reply-review/{id}', [VendorController::class, 'ReplyToReview']);
        Route::delete('/delete-reply/{id}', [VendorController::class, 'DeleteReply']);
        Route::put('/update-reply/{id}', [VendorController::class, 'UpdateReply']);

        // Stats
        Route::get('/total-stats/{id}', [VendorController::class, 'TotalStats']);
        Route::get('/top-performing-months/{id}', [VendorController::class, 'TopPerformingMonths']);
        Route::delete('/delete-account/{id}', [VendorController::class, 'deleteVendorAndData']);
        Route::get('/packages-performance/{id}', [VendorController::class, 'getPackagePerformance']);
    });


    // Public slot routes
    Route::get('/get-slots/{vendorId}/slots', [VendorController::class, 'GetSlotsForDate']);
    Route::get('/get-vendor-available-slots/{vendorId}', [VendorController::class, 'GetVendorAvailableSlots']);
    Route::post('/reactivate-verify', [VendorController::class, 'VendorReactivateVerify']);
});

//pending routes
Route::post('/google-auth', [VendorController::class, 'VendorGoogleSignupOrLogin']);
Route::post('/apple-auth', [VendorController::class, 'VendorAppleSignupOrLogin']);
Route::put('/complete-profile', [VendorController::class, 'ComplteteVendorProfile']);

Route::put('/update-videos/{id}', [VendorController::class, 'UpdateVendorVideos']);
Route::delete('/delete-video/{id}', [VendorController::class, 'DeleteVendorVideo']);


Route::prefix('v1/admin')->group(function () {

    Route::post('/signup', [AdminController::class, 'signup']);
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/verify2fa', [AdminController::class, 'verify2fa']);

    Route::get('/get-all-hosts', [AdminController::class, 'getAllHosts']);
    Route::get('/get-all-bookings', [AdminController::class, 'getAllBookings']);
    Route::patch('/update-host-status/{id}', [AdminController::class, 'updateHostStatus']);

    Route::get('/get-all-vendors', [AdminController::class, 'getAllVendors']);
    Route::patch('/update-vendor-status/{vendorId}', [AdminController::class, 'updateVendorStatus']);

    Route::get('/get-host-by-id/{id}', [AdminController::class, 'getHostById']);
    Route::get('/get-vendor-by-id/{id}', [AdminController::class, 'getVendorById']);
    Route::delete('/delete-vendor-by-id/{id}', [AdminController::class, 'deleteVendorById']);

    Route::post('/create-package', [AdminController::class, 'adminCreatePackage']);
    Route::post('/create-category', [AdminController::class, 'createCategory']);
    Route::put('/update-category/{id}', [AdminController::class, 'updateCategory']);
    Route::post('/create-sub-category', [AdminController::class, 'createSubCategory']);
});

//global routes
Route::prefix('/v1/global')->group(function () {

    // PUBLIC ROUTES
    Route::get('/all-vendors', [\App\Http\Controllers\Global\GlobalController::class, 'GetAllVendors']);
    Route::get('/search-vendors', [GlobalController::class, 'SearchAllVendors']);
    Route::get('/search-vendors-by-category', [GlobalController::class, 'SearchVendorsByCategory']);
    Route::get('/single-vendor/{id}', [GlobalController::class, 'SingleVendor']);

    // CATEGORY ROUTES
    Route::get('/get-categories', [AdminController::class, 'GetAllCategories']);
    Route::get('/get-single-category', [AdminController::class, 'GetSingleCategory']);
    Route::patch('/get-sub-categories', [AdminController::class, 'GetSubCategories']);

    // STATS / PROFILE VIEWS
    Route::post('/view-profile', [GlobalController::class, 'ViewProfile']);
    Route::post('/social-click', [GlobalController::class, 'TrackSocialClick']);

    // SUPPORT
    Route::put('/support-query', [GlobalController::class, 'submitQuery']);

    // VENDOR AVAILABILITY
    Route::get('/search-vendors-by-category-and-date', [\App\Http\Controllers\Host\HostController::class, 'GetAvailableVendors']);

    // TOP-RATED VENDORS
    Route::get('/get-top-rated-vendors', [GlobalController::class, 'GetTopRatedVendors']);

    // COUNTRIES
    Route::post('/add-country', [GlobalController::class, 'addCountry']);
    Route::get('/get-countries', [GlobalController::class, 'getCountries']);
    Route::delete('/delete-country/{name}', [GlobalController::class, 'deleteCountry']);

});
