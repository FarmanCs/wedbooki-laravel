<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Host\Host;
use App\Models\Vendor\Vendor;
use App\Src\Services\EmailService;
use App\Src\Services\OtpService;
use App\Src\Services\JwtService;

class AuthController extends Controller
{
    protected $emailService;
    protected $otpService;
    protected $jwtService;

    public function __construct(
        EmailService $emailService,
        OtpService   $otpService,
        JwtService   $jwtService
    )
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
        $this->jwtService = $jwtService;
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'email' => 'required|email|unique:hosts',
            'password' => 'required|min:8|regex:/[A-Z]/',
            'country_code' => 'required',
            'phone_no' => 'required|unique:hosts',
            'country' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Check if email exists in vendors
        $existingVendor = Vendor::where('email', $request->email)->first();
        if ($existingVendor) {
            return response()->json([
                'message' => 'This email is already registered as a Vendor.'
            ], 409);
        }

        $host = Host::create([
            'full_name' => $request->full_name,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_no' => $request->phone_no,
            'is_verified' => false,
            'signup_method' => 'email',
            'otp' => $this->otpService->generateOtp()
        ]);
        $token = $host->createToken('api_token', ['can:update', 'otp:verify'])->plainTextToken;

        $this->emailService->sendSignupOtp($host, $host->otp);

        return response()->json([
            'message' => 'User created successfully',
            'email' => $host->email,
            'otp' => $host->otp,
            'host_access_token' => $token,
            'user' => $host
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auth' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Determine if auth is email or phone
        $field = filter_var($request->auth, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_no';

        $host = Host::where($field, $request->auth)->first();

        // Check if user exists or is soft-deleted
        if (!$host || $host->account_soft_deleted) {
            // Return a generic error for security, or 'User does not exist' as in your original code
            return response()->json(['message' => 'Invalid credentials or user does not exist'], 401);
        }

        // Check password
        if (!Hash::check($request->password, $host->password)) {
            return response()->json(['message' => 'Invalid credentials or user does not exist'], 401);
        }

        // Check if account is deactivated
        if ($host->account_deactivated) {
            $otp = $this->otpService->generateOtp();
            $host->update(['otp' => $otp]);

            // Note: The original code used sendForgetPasswordOtp, which might be okay,
            // but perhaps a dedicated sendReactivationOtp would be clearer.
            // I will keep the original service call.
            $this->emailService->sendForgetPasswordOtp($host->full_name, $host->email, $otp);

            return response()->json([
                'message' => 'Account is deactivated. OTP sent to email to reactivate.',
                'is_deactivated' => true,
                'user_id' => $host->id
            ], 403);
        }

        // --- Generate Sanctum Token (Consistent with signup) ---
        // Revoke old tokens if necessary (optional, but good practice for single login)
        $host->tokens()->delete();

        // Generate a new token
        $token = $host->createToken('api_token', ['server:update'])->plainTextToken;
        // I removed 'otp:verify' scope as it's not needed for a successful login.

        $completedProfile = $host->wedding_date && $host->event_type;

        return response()->json([
            'message' => 'Logged in successfully',
            'host_access_token' => $token, // Use the same key as in signup
            'user' => $host,
            'completed_profile' => $completedProfile
        ], 200);
    }

    public function verifySignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::where('email', $request->email)->first();

        if (!$host) {
            return response()->json(['message' => 'User does not exist'], 404);
        }

        if ($host->otp != $request->otp) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        // OTP matched, verify the account
        $host->update([
            'otp' => null,
            'is_verified' => true
        ]);

        // --- Generate Sanctum Token (Consistent) ---
        // Revoke any previous signup/OTP tokens
        $host->tokens()->delete();
        $hostAccessToken = $host->createToken('api_token', ['server:update'])->plainTextToken;

        $profileCompleted = $host->event_type && $host->estimated_guests &&
            $host->event_budget && $host->wedding_date;

        return response()->json([
            'message' => 'OTP matched and account verified',
            'success' => true,
            'profile_completed' => $profileCompleted,
            'host_access_token' => $hostAccessToken,
            'user' => $host // Include user data for completeness
        ], 200);
    }

    public function hostVerifyOtp(Request $request)
    {
        try {
            // 1. Get authenticated user ID (like req.userId in Express)
            $userId = $request->user()->id;

            // 2. Get OTP from request
            $otp = $request->input('otp');


            // 3. Find host
            $host = Host::find($userId);
            if (!$host) {
                return response()->json(['message' => 'user not exist'], 404);
            }

            // 4. Check OTP
            if ((int)$host->otp !== (int)$otp) {
                return response()->json(['message' => 'otp not matched'], 404);
            }

            // 5. Clear OTP
            $host->otp = null;
            $host->save();

            // 6. Generate Sanctum token (instead of JWT.sign)
            // Remove old tokens for security
            $host->tokens()->delete();

            $hostAccessToken = $host->createToken('api_token', ['otp:verify'])->plainTextToken;

            // 7. Return response
            return response()->json([
                'message' => 'otp matched',
                'hostAccessToken' => $hostAccessToken
            ], 200);

        } catch (\Exception $error) {
            \Log::error('HostVerifyOtp Error: ' . $error->getMessage());
            return response()->json(['message' => 'try again.Later'], 500);
        }
    }


    public function resendSignupOtp(Request $request)
    {
        // 1. VALIDATION
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // 2. FIND HOST BY EMAIL
        $host = Host::where('email', $request->email)->first();

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // 3. GENERATE NEW OTP
        $otp = $this->otpService->generateOtp();

        $host->update([
            'otp' => $otp
        ]);

        // 4. SEND OTP EMAIL
        $this->emailService->sendSignupOtp($host, $otp);

        // 5. GENERATE NEW SANCTUM TOKEN
        // Remove old tokens
        $host->tokens()->delete();

        $hostAccessToken = $host->createToken('api_token', ['can:update', 'otp:verify'])
            ->plainTextToken;

        // 6. RETURN RESPONSE
        return response()->json([
            'message' => 'OTP sent successfully',
            'email' => $host->email,
            'otp' => $otp, // REMOVE in production if required
            'host_access_token' => $hostAccessToken
        ], 201);
    }


    public function forgetPassword(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'auth' => 'required' // Can be email or phone_no
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // 2. Find User
        $field = filter_var($request->auth, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_no';
        $host = Host::where($field, $request->auth)->first();

        if (!$host) {
            return response()->json(['message' => 'User does not exist'], 404);
        }


        // 3. Generate and Save OTP
        $otp = $this->otpService->generateOtp();
        // Optional: Add an expiry time to the OTP in the Host model
        $host->update(['otp' => $otp]);

        // 4. Send Email/Notification
        $this->emailService->sendForgetPasswordOtp($host, $otp);
//        dd('auth');

        // 5. Response
        return response()->json([
            'message' => 'OTP sent to your email. Use it along with your new password to reset.',
            // NOTE: We don't expose the OTP in production responses.
            'otp' => $otp
        ]);
    }

    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|regex:/[A-Z]/'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Get authenticated user ID (like req.userId in Express)
        $userId = $request->user()->id;

//        dd($userId);

        $host = Host::find($userId);

        if (!$host) {
            return response()->json(['message' => 'User does not exist'], 404);
        }

        $host->update([
            'password' => ($request->password),
            'otp' => null
        ]);

        $this->emailService->sendPasswordResetConfirmation($host);

        $token = $host->createToken('api_token', ['server:update', 'otp:verify'])->plainTextToken;

        return response()->json([
            'message' => 'Password changed successfully',
            'host_access_token' => $token
        ]);
    }


    public function hostUpdatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'currentPassword' => 'required|string',
                'newPassword' => ['required', 'string', 'min:8', 'regex:/[A-Z]/']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Password should be at least 8 characters long and contain at least one uppercase letter'
                ], 400);
            }

            $hostid = auth()->user()->id;
            $currentPassword = $request->input('currentPassword');
            $newPassword = $request->input('newPassword');

            $user = Host::find($hostid);

            if (!$user) {
                return response()->json(['message' => 'user not exist'], 404);
            }

            // Check current password
            if (!Hash::check($currentPassword, $user->password)) {
                return response()->json(['message' => 'current password not matched'], 404);
            }

            // Update password
            $user->password = Hash::make($newPassword);
            $user->save();

            // Send email notification using the new service method
            $this->emailService->sendPasswordUpdateConfirmation($user);

            // Generate new Sanctum token
            $user->tokens()->delete(); // Remove old tokens
            $hostAccessToken = $user->createToken('api_token', ['server:update'])->plainTextToken;

            return response()->json([
                'message' => 'password updated Successfully',
                'hostAccessToken' => $hostAccessToken
            ], 200);

        } catch (\Exception $error) {
            \Log::error('HostUpdatePassword Error: ' . $error->getMessage());
            return response()->json(['message' => 'password updated Successfully']);
        }
    }

    public function passwordChangeRequest()
    {

    }

    public function passwordChangeVerify()
    {

    }

    // Additional methods: regenerateOtp, googleSignupOrLogin, appleSignupOrLogin, etc.


    public function googleLogin()
    {

    }

    public function appleLogin()
    {

    }
}
