<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\SignupRequest;
use App\Http\Requests\Vendor\UpdateBusinessProfileRequest;
use App\Http\Requests\Vendor\UpdateVendorTimingsRequest;
use App\Http\Requests\Vendor\VendorProfileRequest;
use App\Http\Requests\Vendor\VendorUpdateProfileRequest;
use App\Models\Vendor;
use App\Models\Vendor\Business;
use App\Models\Vendor\Timing;
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
use Illuminate\Support\Facades\Validator;

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
    public function VendorSignup(SignupRequest $request)
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

    public function VendorForgetPasswordVerify(Request $request, $id)
    {
        return $this->vendorAuthService->VendorForgetPasswordVerify($request->all(),$id);
    }

    public function VendorResendOtp(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorResendOtp($request->all());
    }

    public function VendorResetPassword(Request $request)
    {
        return $this->vendorAuthService->VendorResetPassword($request->all());
    }

    public function VendorUpdatePassword(Request $request)
    {
        return $this->vendorAuthService->VendorUpdatePassword( $request->all());
    }

    public function VendorChangeEmail(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorChangeEmail( $request->all());
    }

    public function VendorVerifyChangeEmailOtp(Request $request)
    {
        return $this->vendorAuthService->VendorVerifyChangeEmailOtp( $request->all());
    }

    public function VendorPasswordChangeRequest(Request $request)
    {
        return $this->vendorAuthService->VendorPasswordChangeRequest();
    }

    public function VendorPasswordChangeVerify(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorPasswordChangeVerify( $request->all());
    }

    public function VendorDeactivateRequest( ): JsonResponse
    {
        return $this->vendorAuthService->VendorDeactivateRequest();
    }

    public function VendorDeactivateVerify(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorDeactivateVerify( $request->all());
    }

    public function VendorDeleteRequest(): JsonResponse
    {
        return $this->vendorAuthService->VendorDeleteRequest();
    }

    public function VendorDeleteVerify(Request $request): JsonResponse
    {
        return $this->vendorAuthService->VendorDeleteVerify( $request->all());
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

    public function getVendorPersonalProfile(VendorProfileRequest $request): JsonResponse
    {
        return $this->vendorProfileService->getVendorPersonalProfile();
    }

    public function vendorBusinessProfile(): JsonResponse
    {
        return $this->vendorProfileService->vendorBusinessProfile();
    }

    public function updateVendorBusinessProfile(UpdateBusinessProfileRequest $request, $id)
    {
        $result = $this->vendorProfileService->updateVendorBusinessProfile($request, $id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function VendorUpdateProfile(Request $request): JsonResponse
    {
        return $this->vendorProfileService->VendorUpdateProfile($request);
    }

    public function deleteVendorAndData($id): JsonResponse
    {
        return $this->vendorProfileService->deleteVendorAndData($id);
    }

    // Timing Methods
    public function updateVendorTimings(UpdateVendorTimingsRequest $request, $id): JsonResponse
    {
        return $this->vendorTimingService->updateVendorTimings($id, $request);
    }

    public function getServiceVendorTimings($id): JsonResponse
    {
        return $this->vendorTimingService->getServiceVendorTimings($id);
    }

    public function GetVendorVenuTimings($id): JsonResponse
    {
        return $this->vendorTimingService->GetVendorVenuTimings($id);
    }

    public function AddUnavailableDate(Request $request, $businessid): JsonResponse
    {
        $business = Business::where('id', $businessid)->first();

        if(!$business){
            return response()->json(['message' => 'Business not found'], 404);
        }

        // Validate in controller (as you requested)
        $validated = $request->validate([
            'date' => 'required|date'
        ]);


        // Pass only validated data to service
        return $this->vendorTimingService->addUnavailableDate($business->id, $validated);
    }

    public function MakeDateAvailable(Request $request, $businessid): JsonResponse
    {
        // Validate the request
        $validated = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => $validated->errors()->first()
            ], 400);
        }

        // Find the business
        $business = Business::find($businessid);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        // Find the vendor timing
        $timing = Timing::where('business_id', $business->id)->first();
        if (!$timing) {
            return response()->json([
                'message' => 'Vendor timings not found'
            ], 404);
        }

        // Call the service to remove the unavailable date
        return $this->vendorTimingService->makeDateAvailable($request->date,  $timing);
    }

    public function GetUnavailableDates($businessid): JsonResponse
    {
        $businessid=Business::where('id', $businessid)->first();
        if(!$businessid){
            return response()->json(['message' => 'Business not found'], 404);
        }
        return $this->vendorTimingService->getUnavailableDates($businessid);
    }

    public function deleteUnavailableDate(Request $request): JsonResponse
    {
        // Validate the request in the controller
        $validated = $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'date'        => 'required|date',
        ]);

        return $this->vendorTimingService->deleteUnavailableDate($validated);
    }

    public function UpdateUnavailableDates(Request $request): JsonResponse
    {
        // Validate request in controller before passing to service
        $validated = $request->validate([
            'business_id'        => 'required|integer|exists:businesses,id',
            'unavailable_dates'  => 'required|array',
            'unavailable_dates.*'=> 'date', // each item must be a date
        ],[
            'business_id'=>'business id is required',
            'unavailable_dates.*'=>'at least one date should be there to update',
        ]);

        return $this->vendorTimingService->updateUnavailableDates($validated);
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
    public function UpdateVendorPortfolioImages(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->updateVendorPortfolioImages($id, $request);
    }

    public function DeleteVendorPortfolioImage(Request $request, $id): JsonResponse
    {
        return $this->vendorMediaService->deleteVendorPortfolioImage($id, $request);
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
    public function GetVendorBookings($id): JsonResponse
    {
        $business = Business::where('id', $id)->first();
        if(!$business){
            return response()->json(['message' => 'Business not found'], 404);
        }
        return $this->vendorBookingService->getVendorBookings($id);
    }

    public function VendorSingleBooking($id): JsonResponse
    {
        return $this->vendorBookingService->vendorSingleBooking($id);
    }

    public function AcceptBooking(Request $request, $id): JsonResponse
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
