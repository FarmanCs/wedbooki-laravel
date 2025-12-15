<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Host\Host;
use App\Src\Services\EmailService;
use App\Src\Services\OtpService;
use App\Src\Services\JwtService;

class ProfileController extends Controller
{
    protected $emailService;
    protected $otpService;
    protected $jwtService;

    public function __construct(EmailService $emailService, OtpService $otpService, JwtService $jwtService)
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
        $this->jwtService = $jwtService;
    }

    /**
     * Update host profile with S3 upload support
     */
    public function updateProfile(Request $request)
    {
        // -----------------------------
        // Validation
        // -----------------------------
        $validator = Validator::make($request->all(), [
            'event_type'        => 'sometimes|string',
            'estimated_guests'  => 'sometimes|integer',
            'event_budget'      => 'sometimes|numeric',
            'wedding_date'      => 'sometimes|date',
            'partner_full_name' => 'sometimes|string',
            'partner_email'     => 'sometimes|email',
            'full_name'         => 'sometimes|string',
            'about'             => 'sometimes|string',
            'profile_image'     => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        // -----------------------------
        // Authenticated Host
        // -----------------------------
        /** @var Host|null $host */
        $host = auth()->user();


        if (!$host) {
            return response()->json([
                'message' => 'Host not found or unauthenticated'
            ], 401);
        }

        // -----------------------------
        // Update Fields (dynamic)
        // -----------------------------
        $updateFields = [];

        // Handle S3 file upload (using storePublicly like vendor controller)
        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');

                // Store publicly in S3 - this sets proper public-read ACL
                $path = $file->storePublicly('host/profile-images', 's3');

                // Get public URL
                $updateFields['profile_image'] = Storage::disk('s3')->url($path);

                \Log::info('S3 Upload Success: ' . $updateFields['profile_image']);

            } catch (\Exception $e) {
                \Log::error('S3 Upload Error: ' . $e->getMessage());

                return response()->json([
                    'message' => 'Failed to upload file'
                ], 500);
            }
        }

        // Map other fields from request
        if ($request->filled('full_name')) {
            $updateFields['full_name'] = $request->full_name;
        }

        if ($request->filled('about')) {
            $updateFields['about'] = $request->about;
        }

        if ($request->filled('event_type')) {
            $updateFields['event_type'] = $request->event_type;
        }

        if ($request->filled('partner_full_name')) {
            $updateFields['partner_full_name'] = $request->partner_full_name;
        }

        if ($request->filled('partner_email')) {
            $updateFields['partner_email'] = $request->partner_email;
        }

        if ($request->filled('wedding_date')) {
            $updateFields['wedding_date'] = $request->wedding_date;
        }

        if ($request->filled('event_budget')) {
            $updateFields['event_budget'] = (float) $request->event_budget;
        }

        if ($request->filled('estimated_guests')) {
            $updateFields['estimated_guests'] = (int) $request->estimated_guests;
        }

        // -----------------------------
        // Update Host Record
        // -----------------------------
        $host->update($updateFields);

        // -----------------------------
        // Regenerate Token (Sanctum)
        // -----------------------------
        // Revoke old tokens for security
        $host->tokens()->delete();

        $hostAccessToken = $host
            ->createToken('host_access_token', ['host:update', 'otp:verify'])
            ->plainTextToken;

        // -----------------------------
        // Response
        // -----------------------------
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $host->fresh(),
            'profile_image_url' => $updateFields['profile_image'] ?? null,
            'hostAccessToken' => $hostAccessToken,
            'completed_profile' => true
        ], 200);
    }

    /**
     * Get host profile
     */
    public function getProfile(Request $request)
    {
        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'User profile found',
            'user' => $host
        ], 200);
    }

    /**
     * Initiate email change process by sending OTP
     */
    public function changeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:hosts,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate OTP
        $otp = $this->otpService->generateOtp();

        // Save OTP and pending email
        $host->update([
            'email_change_otp' => $otp,
            'updated_email' => $request->email
        ]);

        // Send OTP to current email
        $this->emailService->sendEmailChangeOtp($host, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your current email for verification',
            'new_email' => $request->email
        ], 200);
    }

    /**
     * Verify email change OTP
     */
    public function verifyEmailChangeOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify OTP
        if ($host->email_change_otp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Update email
        $host->update([
            'email' => $host->updated_email,
            'email_change_otp' => null,
            'updated_email' => null
        ]);

        // Regenerate token
        $host->tokens()->delete();
        $newToken = $host->createToken('host_access_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully',
            'user' => $host->fresh(),
            'hostAccessToken' => $newToken
        ], 200);
    }

    /**
     * Update budget
     */
    public function updateBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_budget' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $host->update(['event_budget' => $request->event_budget]);

        return response()->json([
            'success' => true,
            'message' => 'Event budget updated successfully',
            'budget' => $host->event_budget
        ], 200);
    }

    /**
     * Get budget
     */
    public function getBudget()
    {
        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'budget' => $host->event_budget
        ], 200);
    }

    /**
     * Add wedding date
     */
    public function addWeddingDate(Request $request)
    {
        try {
            /** @var Host|null $host */
            $host = auth()->user();

            if (!$host) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 401);
            }

            // Validate request data
            $validator = Validator::make($request->all(), [
                'wedding_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Wedding date is required.',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Update the wedding date
            $host->wedding_date = $request->wedding_date;
            $host->save();

            return response()->json([
                'success' => true,
                'message' => 'Wedding date added successfully.',
                'host' => $host->fresh()
            ], 200);

        } catch (\Exception $e) {
            \Log::error("AddWeddingDate Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }
    }

    /**
     * Delete wedding date
     */
    public function deleteWeddingDate(Request $request)
    {
        try {
            /** @var Host|null $host */
            $host = auth()->user();

            if (!$host) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 401);
            }

            // Check if wedding date exists
            if ($host->wedding_date === null) {
                return response()->json([
                    'message' => 'Wedding date not found.',
                ], 404);
            }

            // Remove wedding_date
            $host->wedding_date = null;
            $host->save();

            return response()->json([
                'success' => true,
                'message' => 'Wedding date deleted successfully.',
                'host' => $host->fresh()
            ], 200);

        } catch (\Exception $e) {
            \Log::error("DeleteWeddingDate Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }
    }

    /**
     * Get wedding date
     */
    public function getWeddingDate()
    {
        try {
            /** @var Host|null $host */
            $host = auth()->user();

            if (!$host) {
                return response()->json([
                    'message' => "Host not found"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'wedding_date' => $host->wedding_date
            ], 200);

        } catch (\Exception $e) {
            \Log::error("GetWeddingDate Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }
    }
}
