<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Host\Host;
use App\Src\Services\FileUploadService;
use App\Src\Services\JwtService;
use App\Src\Services\EmailService;
use App\Src\Services\OtpService;

class ProfileController extends Controller
{
    protected $fileUploadService;
    protected $emailService;
    protected $otpService;
    protected $jwtService;

    public function __construct(FileUploadService $fileUploadService, EmailService $emailService, OtpService $otpService, JwtService $jwtService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->emailService = $emailService;
        $this->otpService = $otpService;
        $this->jwtService = $jwtService;
    }

    /**
     * Update host profile
     */
    public function updateProfile(Request $request)
    {

        // Validation
        $validator = Validator::make($request->all(), [
            'event_type' => 'sometimes|string',
            'estimated_guests' => 'sometimes|integer',
            'event_budget' => 'sometimes|numeric',
            'wedding_date' => 'sometimes|date',
            'partner_full_name' => 'sometimes|string',
            'partner_email' => 'sometimes|email',
            'full_name' => 'sometimes|string',
            'about' => 'sometimes|string',
            'profile_image' => 'sometimes|file|image|max:5120', // max 5MB
        ]);
//        dd("testing");


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // Get authenticated host using the 'host' guard
        /** @var Host|null $host */
        $host = auth()->user();

        if (!$host) {
            return response()->json(['message' => 'Host not found or unauthenticated'], 401);
        }

        // Collect fields dynamically
        $updateData = $request->only([
            'full_name', 'about', 'event_type', 'partner_full_name', 'partner_email'
        ]);

        if ($request->has('wedding_date')) {
            $updateData['wedding_date'] = $request->wedding_date;
        }

        if ($request->has('event_budget')) {
            $updateData['event_budget'] = $request->event_budget;
        }

        if ($request->has('estimated_guests')) {
            $updateData['estimated_guests'] = $request->estimated_guests;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            try {
                $updateData['profile_image'] = $this->fileUploadService->upload($request->file('profile_image'));
            } catch (\Exception $e) {
                \Log::error('File upload failed: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to upload file'], 500);
            }
        }

        // Update host record
        $host->update($updateData);
        // Revoke old tokens and generate new Sanctum token
        $hostAccessToken = $host->createToken('api_token', ['host:update', 'otp:verify'])->plainTextToken;

        // Return response
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $host->fresh(),
            'profile_image_url' => $host->profile_image ?? null,
            'host_access_token' => $hostAccessToken,
            'completed_profile' => true
        ]);
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
        ]);
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
        $otp = $host->createToken('access_token', ['otp:verify'])->plainTextToken;

        // Save OTP and pending email
        $host->update([
            'updated_email' => $request->email
        ]);

        // Send OTP to current email
        $this->emailService->sendEmailChangeOtp($host, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email for verification',
            'email' => $request->email
        ]);
    }


    public function updateBudget(Request $request)
    {
        $validator = Validator::make($request->all(), ['event_budget' => 'required']);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()], 400);
        }

        $host = auth()->user();

        $host->update(['event_budget' => $request->event_budget]);

        return response()->json([
            'success' => true,
            'message' => 'Event budget saved',
            'budget' => $host->event_budget
        ]);
    }

    public function getBudget()
    {
        $host = auth()->user();
        return response()->json([
            'success' => true,
            'budget' => $host->event_budget
        ]);
    }


    public function verifyChangeEmailOtp()
    {

    }

    public function addWeddingDate(Request $request)
    {
        try {
            // Get authenticated host (Sanctum)
            $id = auth()->id();


            if (!$id) {
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
                    'message' => 'Host ID and wedding date are required.',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Find host by id

            $host = Host::find($id);

            if (!$host) {
                return response()->json([
                    'message' => 'Host not found.'
                ], 404);
            }

            // Update the wedding date
            $host->wedding_date = $request->wedding_date;
            $host->save();

            return response()->json([
                'message' => 'Wedding date added successfully.',
                'host' => $host
            ], 200);

        } catch (\Exception $e) {
            \Log::error("AddWeddingDate Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function deleteWeddingDate(Request $request)
    {
        try {
            // Get authenticated host
            $host = auth()->user(); // or auth('host')->user()

            if (!$host) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 401);
            }

            // If there is no wedding date set
            if ($host->wedding_date === null) {
                return response()->json([
                    'message' => 'Wedding date not found.',
                ], 404);
            }

            // Remove wedding_date (equivalent to $unset in MongoDB)
            $host->wedding_date = null;
            $host->save();

            return response()->json([
                'message' => 'Wedding date deleted successfully.',
                'host' => $host
            ], 200);

        } catch (\Exception $e) {
            \Log::error("DeleteWeddingDate Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }
    }


    public function getWeddingDate()
    {
        try {
            $host = auth()->user();
            if (!$host) {
                return response()->json(["Message" => "Host not found"], 404);
            }
            $host = Host::find($host->id)->wedding_date;
            return response()->json([ 'Host_date'=> $host]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error.'
            ], 500);
        }

    }
}
