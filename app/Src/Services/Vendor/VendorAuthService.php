<?php

namespace App\Src\Services\Vendor;

use App\Models\SubCategory;
use App\Models\Vendor\Category;
use App\Models\Vendor\Vendor;
use App\Models\Host\Host;
use App\Models\Vendor\Business;
use App\Mail\Vendor\SignupOtpMail;
use App\Mail\Vendor\ForgetPasswordMail;
use App\Mail\Vendor\ResetPasswordMail;
use App\Mail\Vendor\UpdatePasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
//use Google\Client as GoogleClient;
//use Firebase\JWT\JWT;
//use Firebase\JWT\Key;

class VendorAuthService
{
    protected $counterService;

    public function __construct(CounterService $counterService)
    {
        $this->counterService = $counterService;
    }

    private function generateOtp(): int
    {
        return rand(1000, 9999);
    }

    public function signup(Request $request)
    {
        // VALIDATION
        $validated = $request->validate([
            'full_name'              => 'required|string|max:255',
            'company_name'           => 'required|string|max:255',
            'category_id'            => 'required|exists:categories,id',
            'sub_category_id'        => 'nullable|exists:sub_categories,id',
            'country'                => 'required|string',
            'city'                   => 'required|string',
            'email'                  => 'required|email|unique:vendors,email|unique:hosts,email',
            'phone_no'               => 'required|numeric|unique:vendors,phone_no|unique:hosts,phone_no',
            'country_code'           => 'required|string',
            'password'               => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/', // must contain uppercase
            ],
            'business_registration'   => 'nullable|string',
            'business_license_number' => 'nullable|string',
        ], [
            'category_id.exists'      => 'The selected category does not exist.',
            'sub_category_id.exists'  => 'The selected sub category does not exist.',
            'email.unique'            => 'This email is already registered.',
            'phone_no.unique'         => 'This phone number is already registered.',
            'password.regex'          => 'Password must contain at least one uppercase letter.',
        ]);

        // LOWERCASE EMAIL
        $email = strtolower($validated['email']);

        // VENDOR TYPE
        $vendorType = (int)$validated['category_id'] === 1 ? 'venue' : 'service';

        // CUSTOM ID
        $customVendorId = $this->counterService->getNextCounter('vendor_id', 'WB-V300');

        // CREATE BUSINESS PROFILE
        $businessProfile = Business::create([
            'company_name'            => $validated['company_name'],
            'category_id'             => $validated['category_id'],
            'sub_category_id'         => $validated['sub_category_id'] ?? null,
            'business_registration'   => $validated['business_registration'] ?? null,
            'business_license_number' => $validated['business_license_number'] ?? null,
            'vendor_type'             => $vendorType,
        ]);

        // GENERATE OTP
        $otp = $this->generateOtp();

        // CREATE VENDOR
        $vendor = Vendor::create([
            'full_name'        => $validated['full_name'],
            'country'          => $validated['country'],
            'city'             => $validated['city'],
            'email'            => $email,
            'phone_no'         => $validated['phone_no'],
            'country_code'     => $validated['country_code'],
            'category'         => $validated['category_id'],
            'password'         => Hash::make($validated['password']),
            'custom_vendor_id' => $customVendorId,
            'business_id'      => $businessProfile->id,
            'otp'              => $otp,
        ]);

        // SEND OTP EMAIL
        Mail::to($email)->send(new SignupOtpMail($validated['full_name'], $otp));

        return response()->json([
            'success' => true,
            'message' => 'Vendor registered successfully. OTP has been sent to email.',
            'vendor'  => $vendor,
        ], 201);
    }

    public function verifySignup(array $data)
    {
        $validated = Validator::make($data, [
            'email' => [
                'required',
                'email',
                'exists:vendors,email'
            ],
            'otp'   => 'required|numeric',
        ], [
            'email.required' => 'Email is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.exists'   => 'No vendor account found with this email.',
            'otp.required'   => 'OTP is required.',
            'otp.numeric'    => 'OTP must be a number.',
        ])->validate();
        $vendor = Vendor::where('email', strtolower($validated['email']))->firstOrFail();

        Validator::make($validated, [
            'otp' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($vendor) {
                    if ((int) $vendor->otp !== (int) $value) {
                        $fail('Invalid OTP. Please try again.');
                    }
                },
            ],
        ])->validate();

        // MARK VENDOR AS VERIFIED
        $vendor->update([
            'otp'            => null,
            'email_verified' => true,
            'is_verified'    => true,
        ]);

        // ISSUE ACCESS TOKEN
        $token = $vendor->createToken('vendorAccessToken')->plainTextToken;

        return response()->json([
            'success'           => true,
            'message'           => 'OTP verified successfully.',
            'vendorAccessToken' => $token
        ], 200);
    }

    public function googleAuth(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $client = new GoogleClient(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($data['id_token']);

            if (!$payload) {
                return response()->json(['message' => 'Invalid Google token'], 400);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $fullName = $payload['name'];

            $vendor = Vendor::where('google_id', $googleId)->first();

            if (!$vendor) {
                if (Vendor::where('email', $email)->exists()) {
                    return response()->json(['message' => 'Email already registered with another method'], 409);
                }

                if (Host::where('email', $email)->exists()) {
                    return response()->json([
                        'message' => 'This email is already registered as a Host. You cannot signup as a Vendor. Please use another email.'
                    ], 409);
                }

                $customVendorId = $this->counterService->getNextCounter('vendor_id', 'WB-V300');

                $vendor = Vendor::create([
                    'full_name' => $fullName,
                    'email' => $email,
                    'google_id' => $googleId,
                    'signup_method' => 'google',
                    'custom_vendor_id' => $customVendorId,
                    'email_verified' => true,
                    'is_verified' => true,
                    'password' => Hash::make(Str::random(16)),
                ]);
            }

            $completedProfile = (bool)($vendor->category && $vendor->phone_no);
            $token = $vendor->createToken('vendorAccessToken')->plainTextToken;

            return response()->json([
                'message' => 'Vendor authenticated with Google successfully',
                'vendorAccessToken' => $token,
                'user' => $vendor,
                'completedProfile' => $completedProfile,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Google login/signup failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function appleAuth(array $data): JsonResponse
    {
        $idToken = $data['authorization']['id_token'] ?? $data['id_token'] ?? null;

        if (!$idToken) {
            return response()->json(['message' => 'Apple ID token is required'], 400);
        }

        try {
            $decoded = JWT::decode($idToken, new Key('', 'RS256'), ['decode' => true]);

            if (!$decoded) {
                return response()->json(['message' => 'Failed to decode Apple ID token'], 400);
            }

            $appleId = $decoded->sub ?? null;
            $email = $decoded->email ?? null;

            if (!$appleId) {
                return response()->json(['message' => 'Apple ID not found in token'], 400);
            }

            $fullName = 'Apple User';
            if (isset($data['user']['name'])) {
                $firstName = $data['user']['name']['firstName'] ?? '';
                $lastName = $data['user']['name']['lastName'] ?? '';
                $fullName = trim("$firstName $lastName") ?: 'Apple User';
            }

            $vendor = Vendor::where('apple_id', $appleId)->first();

            if (!$vendor && $email) {
                $vendor = Vendor::where('email', $email)->first();
            }

            if (!$vendor) {
                if ($email && Host::where('email', $email)->exists()) {
                    return response()->json([
                        'message' => 'This email is already registered as a Host. You cannot sign up as a Vendor. Please use another email.'
                    ], 409);
                }

                $customVendorId = $this->counterService->getNextCounter('vendor_id', 'WB-V300');

                $vendor = Vendor::create([
                    'full_name' => $fullName,
                    'email' => $email,
                    'apple_id' => $appleId,
                    'signup_method' => 'apple',
                    'custom_vendor_id' => $customVendorId,
                    'is_verified' => true,
                    'email_verified' => (bool)$email,
                    'password' => Hash::make(Str::random(16)),
                ]);
            } else {
                if (isset($data['user']['name']) && $vendor->full_name === 'Apple User') {
                    $vendor->full_name = $fullName;
                }

                if (!$vendor->apple_id) {
                    $vendor->apple_id = $appleId;
                }

                $vendor->save();
            }

            $token = $vendor->createToken('vendorAccessToken')->plainTextToken;
            $completedProfile = (bool)($vendor->category && $vendor->phone_no);

            return response()->json([
                'message' => 'Logged in successfully',
                'vendorAccessToken' => $token,
                'completedProfile' => $completedProfile,
                'user' => $vendor,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error logging in',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function VendorLogin(array $data)
    {
        $validated = Validator::make($data, [
            'auth'     => 'required|string',
            'password' => 'required|string',
        ], [
            'auth.required'     => 'Email or phone number is required.',
            'password.required' => 'Password is required.',
        ])->validate();
        $auth = $validated['auth'];
        // DETERMINE LOGIN FIELD
        $query = is_numeric($auth)
            ? ['phone_no' => (int) $auth]
            : ['email' => strtolower($auth)];
        // FETCH VENDOR WITH RELATIONS
        $vendor = Vendor::where($query)
            ->with(['category', 'business'])
            ->first();

        // USER NOT FOUND OR SOFT DELETED
        if (!$vendor || $vendor->account_soft_deleted) {
            return response()->json([
                'message' => 'User does not exist.'
            ], 404);
        }
        // ACCOUNT DEACTIVATED
        if ($vendor->account_deactivated) {
            $otp = $this->generateOtp();
            $vendor->update(['otp' => $otp]);

            Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

            return response()->json([
                'message'       => 'Account is deactivated. OTP sent to email to reactivate.',
                'isDeactivated' => true,
                'userId'        => $vendor->id,
            ], 403);
        }

        // PASSWORD CHECK
        if (!Hash::check($validated['password'], $vendor->password)) {
            return response()->json([
                'message' => 'Invalid password.'
            ], 400);
        }

        // CREATE ACCESS TOKEN
        $token = $vendor->createToken('vendorAccessToken')->plainTextToken;

        // CHECK IF PROFILE IS COMPLETED
        $completedProfile = (bool) ($vendor->category && $vendor->phone_no);

        // PREPARE VENDOR DATA (without password)
        $vendorData = $vendor->toArray();
        unset($vendorData['password']);

        return response()->json([
            'message'           => 'Logged in successfully.',
            'vendorAccessToken' => $token,
            'user'              => $vendorData,
            'completedProfile'  => $completedProfile,
        ], 200);
    }

    public function VendorForgetPassword(array $data)
    {
        $validator = Validator::make($data, [
            'auth' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $auth = $data['auth'];
        $query = is_numeric($auth)
            ? ['phone_no' => (int)$auth]
            : ['email' => strtolower($auth)];

        $vendor = Vendor::where($query)->first();

        if (!$vendor) {
            return response()->json(['message' => 'User does not exist'], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json([
            'message' => 'OTP sent to your email',
            'otp' => $otp
        ], 200);
    }

    public function verifyOtp(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::where('email', $data['email'])->first();

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json(['message' => 'OTP not matched'], 404);
        }

        $vendor->otp = null;
        $vendor->save();

        return response()->json(['message' => 'OTP matched'], 200);
    }

    public function resendOtp(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::where('email', strtolower(trim($data['email'])))->first();

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json(['message' => 'OTP resend to your email'], 200);
    }

    public function resetPassword(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:8|regex:/[A-Z]/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::where('email', $data['email'])->first();

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        $vendor->password = Hash::make($data['password']);
        $vendor->otp = null;
        $vendor->save();

        Mail::to($vendor->email)->send(new ResetPasswordMail($vendor->full_name));

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function updatePassword($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|regex:/[A-Z]/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if (!Hash::check($data['currentPassword'], $vendor->password)) {
            return response()->json(['message' => 'Current password not matched'], 404);
        }

        $vendor->password = Hash::make($data['newPassword']);
        $vendor->save();

        Mail::to($vendor->email)->send(new UpdatePasswordMail($vendor->full_name));

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function changeEmail($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $email = strtolower($data['email']);

        if (Vendor::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exist'
            ], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'User not exist'
            ], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->pending_email = $email;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email',
            'email' => $email
        ], 200);
    }

    public function verifyChangeEmailOtp($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'User not exist'
            ], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'OTP not matched'
            ], 400);
        }

        $vendor->email = $vendor->pending_email;
        $vendor->pending_email = null;
        $vendor->otp = null;
        $vendor->save();

        $token = $vendor->createToken('vendorAccessToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully',
            'vendorAccessToken' => $token
        ], 200);
    }

    public function passwordChangeRequest($id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ($vendor->account_soft_deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json(['message' => 'OTP sent to your email'], 200);
    }

    public function passwordChangeVerify($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'otp' => 'required|numeric',
            'newPassword' => 'required|string|min:8|regex:/[A-Z]/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ($vendor->account_soft_deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $vendor->password = Hash::make($data['newPassword']);
        $vendor->otp = null;
        $vendor->save();

        Mail::to($vendor->email)->send(new ResetPasswordMail($vendor->full_name));

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function deactivateRequest($id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json(['message' => 'OTP sent to your email to confirm deactivation'], 200);
    }

    public function deactivateVerify($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $vendor->account_deactivated = true;
        $vendor->otp = null;
        $vendor->save();

        return response()->json(['message' => 'Account deactivated successfully'], 200);
    }

    public function deleteRequest($id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        $otp = $this->generateOtp();
        $vendor->otp = $otp;
        $vendor->save();

        Mail::to($vendor->email)->send(new ForgetPasswordMail($vendor->full_name, $otp));

        return response()->json(['message' => 'OTP sent to your email to confirm account deletion'], 200);
    }

    public function deleteVerify($id, array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'User not exist'], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $vendor->account_soft_deleted = true;
        $vendor->account_soft_deleted_at = now();
        $vendor->otp = null;
        $vendor->save();

        return response()->json(['message' => 'Account soft-deleted successfully'], 200);
    }

    public function reactivateVerify(array $data): JsonResponse
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $vendor = Vendor::where('email', $data['email'])->first();

        if (!$vendor) {
            return response()->json(['message' => 'User does not exist'], 404);
        }

        if ((int)$vendor->otp !== (int)$data['otp']) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $vendor->otp = null;
        $vendor->account_deactivated = false;
        $vendor->save();

        $token = $vendor->createToken('vendorAccessToken')->plainTextToken;
        $vendorData = $vendor->toArray();
        unset($vendorData['password']);

        return response()->json([
            'message' => 'Account reactivated',
            'vendorAccessToken' => $token,
            'user' => $vendorData
        ], 200);
    }
}
