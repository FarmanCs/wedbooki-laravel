<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Src\Services\Vendor\VendorAuthService;
use App\Src\Services\Vendor\VendorProfileService;
use App\Src\Services\Vendor\VendorTimingService;
use App\Src\Services\Vendor\VendorMediaService;
use App\Src\Services\Vendor\VendorBookingService;
use App\Src\Services\Vendor\VendorPackageService;
use App\Src\Services\Vendor\VendorReviewService;
use App\Src\Services\Vendor\VendorStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    protected $vendorAuthService;
    protected $vendorProfileService;
    protected $vendorTimingService;
    protected $vendorMediaService;
    protected $vendorBookingService;
    protected $vendorPackageService;
    protected $vendorReviewService;
    protected $vendorStatsService;

    public function __construct(
        VendorAuthService $vendorAuthService,
        VendorProfileService $vendorProfileService,
        VendorTimingService $vendorTimingService,
        VendorMediaService $vendorMediaService,
        VendorBookingService $vendorBookingService,
        VendorPackageService $vendorPackageService,
        VendorReviewService $vendorReviewService,
        VendorStatsService $vendorStatsService
    ) {
        $this->vendorAuthService = $vendorAuthService;
        $this->vendorProfileService = $vendorProfileService;
        $this->vendorTimingService = $vendorTimingService;
        $this->vendorMediaService = $vendorMediaService;
        $this->vendorBookingService = $vendorBookingService;
        $this->vendorPackageService = $vendorPackageService;
        $this->vendorReviewService = $vendorReviewService;
        $this->vendorStatsService = $vendorStatsService;
    }

    // Authentication Methods
    public function VendorSignup(Request $request)
    {

        return $this->vendorAuthService->signup($request);
    }

    public function verifySignup(Request $request)
    {
        return $this->vendorAuthService->verifySignup($request->all());
    }

    public function googleAuth(Request $request): JsonResponse
    {
        return $this->vendorAuthService->googleAuth($request->all());
    }

    public function appleAuth(Request $request): JsonResponse
    {
        return $this->vendorAuthService->appleAuth($request->all());
    }

    public function VendorLogin(Request $request)
    {
        return $this->vendorAuthService->VendorLogin($request->all());
    }

    public function VendorForgetPassword(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorForgetPassword($request->all());
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->vendorAuthService->verifyOtp($request->all());
    }

    public function resendOtp(Request $request): JsonResponse
    {
        return $this->vendorAuthService->resendOtp($request->all());
    }

    public function resetPassword(Request $request): JsonResponse
    {
        return $this->vendorAuthService->resetPassword($request->all());
    }

    public function updatePassword(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->updatePassword($id, $request->all());
    }

    public function changeEmail(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->changeEmail($id, $request->all());
    }

    public function verifyChangeEmailOtp(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->verifyChangeEmailOtp($id, $request->all());
    }

    public function passwordChangeRequest(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->passwordChangeRequest($id);
    }

    public function passwordChangeVerify(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->passwordChangeVerify($id, $request->all());
    }

    public function deactivateRequest(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->deactivateRequest($id);
    }

    public function deactivateVerify(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->deactivateVerify($id, $request->all());
    }

    public function deleteRequest(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->deleteRequest($id);
    }

    public function deleteVerify(Request $request, $id): JsonResponse
    {
        return $this->vendorAuthService->deleteVerify($id, $request->all());
    }

    public function reactivateVerify(Request $request): JsonResponse
    {
        return $this->vendorAuthService->reactivateVerify($request->all());
    }

    // Profile Methods
    public function completeProfile(Request $request): JsonResponse
    {
        return $this->vendorProfileService->completeProfile($request->all());
    }

    public function updateVendorProfile(Request $request, $id): JsonResponse
    {
        return $this->vendorProfileService->updateVendorProfile($id, $request->all(), $request->file('profileImage'));
    }

    public function getVendorPersonalProfile($id): JsonResponse
    {
        return $this->vendorProfileService->getVendorPersonalProfile($id);
    }

    public function vendorBusinessProfile($id): JsonResponse
    {
        return $this->vendorProfileService->vendorBusinessProfile($id);
    }

    public function updateVendorBusinessProfile(Request $request, $id): JsonResponse
    {
        return $this->vendorProfileService->updateVendorBusinessProfile($id, $request->all());
    }

    public function deleteVendorAndData($id): JsonResponse
    {
        return $this->vendorProfileService->deleteVendorAndData($id);
    }

    // Timing Methods
    public function updateVendorTimings(Request $request, $id): JsonResponse
    {
        return $this->vendorTimingService->updateVendorTimings($id, $request->all());
    }

    public function getServiceVendorTimings($id): JsonResponse
    {
        return $this->vendorTimingService->getServiceVendorTimings($id);
    }

    public function getVenueVendorTimings($id): JsonResponse
    {
        return $this->vendorTimingService->getVenueVendorTimings($id);
    }

    public function addUnavailableDate(Request $request, $id): JsonResponse
    {
        return $this->vendorTimingService->addUnavailableDate($id, $request->all());
    }

    public function makeDateAvailable(Request $request, $id): JsonResponse
    {
        return $this->vendorTimingService->makeDateAvailable($id, $request->all());
    }

    public function getUnavailableDates($id): JsonResponse
    {
        return $this->vendorTimingService->getUnavailableDates($id);
    }

    public function deleteUnavailableDate(Request $request): JsonResponse
    {
        return $this->vendorTimingService->deleteUnavailableDate($request->all());
    }

    public function updateUnavailableDates(Request $request): JsonResponse
    {
        return $this->vendorTimingService->updateUnavailableDates($request->all());
    }

    public function getSlotsForDate($vendorId): JsonResponse
    {
        return $this->vendorTimingService->getSlotsForDate($vendorId);
    }

    public function getVendorAvailableSlots($vendorId): JsonResponse
    {
        return $this->vendorTimingService->getVendorAvailableSlots($vendorId);
    }

    // Media Methods
    public function updateVendorPortfolioImages(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->updateVendorPortfolioImages($id, $request->allFiles());
    }

    public function deleteVendorPortfolioImage(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->deleteVendorPortfolioImage($id, $request->all());
    }

    public function updateVendorVideos(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->updateVendorVideos($id, $request->allFiles());
    }

    public function deleteVendorVideo(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->deleteVendorVideo($id, $request->all());
    }

    // Booking Methods
    public function getVendorBookings($id): JsonResponse
    {
        return $this->vendorBookingService->getVendorBookings($id);
    }

    public function vendorSingleBooking($id): JsonResponse
    {
        return $this->vendorBookingService->vendorSingleBooking($id);
    }

    public function acceptBooking(Request $request, $id): JsonResponse
    {
        return $this->vendorBookingService->acceptBooking($id, $request->all());
    }

    public function rejectBooking(Request $request, $id): JsonResponse
    {
        return $this->vendorBookingService->rejectBooking($id, $request->all());
    }

    // Package Methods
    public function createPackage(Request $request, $id): JsonResponse
    {

        return $this->vendorPackageService->createPackage($id, $request->all());
    }

    public function updatePackage(Request $request, $id): JsonResponse
    {
        return $this->vendorPackageService->updatePackage($id, $request->all());
    }

    public function deletePackage(Request $request, $id): JsonResponse
    {
        return $this->vendorPackageService->deletePackage($id, $request->all());
    }

    public function getAllPackages($id): JsonResponse
    {
        return $this->vendorPackageService->getAllPackages($id);
    }

    // Service Methods
    public function createService(Request $request, $id): JsonResponse
    {
        return $this->vendorPackageService->createService($id, $request->all());
    }

    public function updateService(Request $request, $id): JsonResponse
    {
        return $this->vendorPackageService->updateService($id, $request->all());
    }

    public function deleteService(Request $request, $id): JsonResponse
    {
        return $this->vendorPackageService->deleteService($id, $request->all());
    }

    // Review Methods
    public function getAllMyReviews($id): JsonResponse
    {
        return $this->vendorReviewService->getAllMyReviews($id);
    }

    public function replyToReview(Request $request, $id): JsonResponse
    {
        return $this->vendorReviewService->replyToReview($id, $request->all());
    }

    public function updateReply(Request $request, $id): JsonResponse
    {
        return $this->vendorReviewService->updateReply($id, $request->all());
    }

    public function deleteReply(Request $request, $id): JsonResponse
    {
        return $this->vendorReviewService->deleteReply($id, $request->all());
    }

    // Stats Methods
    public function totalStats($id): JsonResponse
    {
        return $this->vendorStatsService->totalStats($id);
    }

    public function topPerformingMonths(Request $request, $id): JsonResponse
    {
        return $this->vendorStatsService->topPerformingMonths($id, $request->all());
    }

    public function getPackagePerformance(Request $request, $id): JsonResponse
    {
        return $this->vendorStatsService->getPackagePerformance($id, $request->all());
    }
}
