<?php

namespace App\Src\Services\Vendor;

use App\Models\BusinessSocialClick;
use App\Models\Chat;
use App\Models\Host\Review;
use App\Models\Message;
use App\Models\Vendor;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Timing;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorProfileService
{
    protected $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function completeProfile(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'company_name' => 'required|string',
            'category' => 'required|string',
            'subcategory' => 'nullable|string|size:24',
            'email' => 'required|email',
            'phone_no' => 'required|numeric',
            'country_code' => 'required|string',
            'country' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $email = strtolower($data['email']);
        $vendor = Vendor::where('email', $email)->with('businessProfile')->first();

        if (!$vendor) {
            return response()->json(['message' => 'Email not exist'], 409);
        }

        $existPhoneNo = Vendor::where('phone_no', $data['phone_no'])
            ->where('id', '!=', $vendor->id)
            ->exists();

        if ($existPhoneNo) {
            return response()->json(['message' => 'Phone number already exists. Please use another.'], 409);
        }

        // Update vendor fields
        foreach ($data as $key => $value) {
            if ($value && $vendor->{$key} !== $value) {
                $vendor->{$key} = $value;
            }
        }

        // Update or create business profile
        if ($vendor->business_profile_id) {
            $businessProfile = Business::find($vendor->business_profile_id);
            $businessProfile->company_name = $data['company_name'];
            $businessProfile->category = $data['category'];

            if (!empty($data['subcategory']) && strlen($data['subcategory']) === 24) {
                $businessProfile->subcategory = $data['subcategory'];
            }

            $businessProfile->save();
        } else {
            $businessData = [
                'company_name' => $data['company_name'],
                'category' => $data['category'],
            ];

            if (!empty($data['subcategory']) && strlen($data['subcategory']) === 24) {
                $businessData['subcategory'] = $data['subcategory'];
            }

            $businessProfile = Business::create($businessData);
            $vendor->business_profile_id = $businessProfile->id;
        }

        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => $vendor->business_profile_id ? 'Profile updated successfully' : 'Profile completed successfully'
        ], 201);
    }

    public function updateVendorProfile($id, array $data, $file = null): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $updateFields = [];

        // Handle file upload
        if ($file) {
            try {
                $s3Url = $this->s3Service->uploadFile($file);
                $updateFields['profile_image'] = $s3Url;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to upload file'], 500);
            }
        }

        // Map fields
        $fieldMapping = [
            'name' => 'full_name',
            'years_of_experince' => 'years_of_experince',
            'team_members' => 'team_members',
            'phone_no' => 'phone_no',
            'country_code' => 'country_code',
            'country' => 'country',
            'about' => 'about',
            'city' => 'city',
        ];

        foreach ($fieldMapping as $requestKey => $dbKey) {
            if (isset($data[$requestKey])) {
                $updateFields[$dbKey] = $data[$requestKey];
            }
        }

        // Handle arrays
        if (isset($data['languages'])) {
            $updateFields['languages'] = is_array($data['languages'])
                ? $data['languages']
                : array_map('trim', explode(',', $data['languages']));
        }

        if (isset($data['specialties'])) {
            $updateFields['specialties'] = is_array($data['specialties'])
                ? $data['specialties']
                : array_map('trim', explode(',', $data['specialties']));
        }

        $vendor->update($updateFields);

        return response()->json([
            'message' => 'Profile updated',
            'vendor' => $vendor->fresh(),
            'ProfileImage' => $updateFields['profile_image'] ?? null
        ], 200);
    }

    public function getVendorPersonalProfile($id): JsonResponse
    {
        $vendor = Vendor::select('*')->where('id', $id)->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return response()->json(['vendor' => $vendor], 200);
    }

    public function vendorBusinessProfile($id): JsonResponse
    {
        $vendor = Vendor::with(['businessProfile.category'])->find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ($vendor->role !== 'vendor') {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access this profile'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile found',
            'user' => $vendor
        ], 200);
    }

    public function updateVendorBusinessProfile($id, array $data): JsonResponse
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found'
            ], 404);
        }

        $allowedFields = [
            'company_name',
            'business_desc',
            'features',
            'businessEmail',
            'businessPhone',
            'country',
            'city',
            'zip',
            'street_address',
            'website',
            'social_links',
            'postal_code',
            'capacity',
            'services',
            'addi_services',
            'vendor_type',
            'category',
            'faqs',
            'PaymentDaysAdvance',
            'PaymentDaysFinal',
            'ServicesRadius',
            'advancePercentage',
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        $business->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Business profile updated successfully',
            'data' => $business->fresh()
        ], 200);
    }

    public function deleteVendorAndData($id): JsonResponse
    {
        $vendor = Vendor::with('businessProfile')->find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Collect images to delete
        $vendorImages = array_filter([
            $vendor->profile_image,
            $vendor->cover_image,
        ]);

        $business = $vendor->businessProfile;
        $portfolioMedia = [];

        if ($business) {
            if (is_array($business->portfolio_images)) {
                $portfolioMedia = array_merge($portfolioMedia, array_filter($business->portfolio_images));
            }

            if (is_array($business->videos)) {
                $portfolioMedia = array_merge($portfolioMedia, array_filter($business->videos));
            }
        }

        // Delete related data
        Booking::where('venue_id', $vendor->id)->delete();

        // Delete chats and messages
        $chats = Chat::whereHas('participants', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->pluck('id');

        Message::whereIn('chat_id', $chats)->delete();
        Chat::whereIn('id', $chats)->delete();

        // Delete reviews and replies
        if ($business) {
            $reviews = Review::where('business_id', $business->id)->pluck('id');
            Review::where('vendor_id', $vendor->id)->delete();
            Review::whereIn('id', $reviews)->update(['vendor_replies' => []]);

            BusinessSocialClick::where('business_id', $business->id)->delete();
            Timing::where('business_id', $business->id)->delete();

            $business->delete();
        }

        // Delete media files from S3
        $allMedia = array_merge($vendorImages, $portfolioMedia);
        foreach ($allMedia as $fileUrl) {
            $this->s3Service->deleteByUrl($fileUrl);
        }

        // Delete vendor
        $vendor->delete();

        return response()->json([
            'message' => 'Vendor and all related data deleted successfully.'
        ], 200);
    }
}
