<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host\Host;
use App\Models\Host\Checklist;
use App\Models\Host\GuestGroup;
use App\Models\Host\Favorite;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Vendor\Booking;
use App\Src\Services\EmailService;
use App\Src\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    protected $emailService;
    protected $otpService;

    public function __construct(EmailService $emailService, OtpService $otpService)
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
    }

    // Host Deactivation Request

    public function deactivateRequest(Request $request)
    {
        try {
            $host = auth()->user(); // Sanctum Auth

            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            if ($host->account_soft_deleted) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $otp = $this->otpService->generateOtp();
            $host->update(['otp' => $otp]);

//            $this->emailService->sendForgetPasswordOtp($host->full_name, $host->email, $otp);
            $this->emailService->sendAccountDeactivationOtp($host, $otp);
            return response()->json([
                'message' => 'OTP sent to your email to confirm deactivation'
            ], 200);

        } catch (\Exception $e) {
            Log::error("HostDeactivateRequest Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Host Deactivation Verify
    public function deactivateVerify(Request $request)
    {
        try {
            $host = auth()->user(); // Sanctum Auth

            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            if ($host->account_soft_deleted) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $validator = Validator::make($request->all(), [
                'otp' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            if ((int)$host->otp !== (int)$request->otp) {
                return response()->json(['message' => 'OTP not matched'], 400);
            }

            $host->update([
                'account_deactivated' => true,
                'otp' => null
            ]);

            return response()->json([
                'message' => 'Account deactivated successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error("HostDeactivateVerify Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Host Delete Request (Soft Delete)
    public function deleteRequest(Request $request)
    {
        try {
            $host = auth()->user(); // Sanctum auth

            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            if ($host->account_soft_deleted) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Generate OTP
            $otp = $this->otpService->generateOtp();

            // Save OTP to host
            $host->update(['otp' => $otp]);

            // Send account deletion OTP via Mailable
            $this->emailService->sendAccountDeletionOtp($host, $otp);

            return response()->json([
                'message' => 'OTP sent to your email to confirm account deletion'
            ], 200);

        } catch (\Exception $e) {
            Log::error("HostDeleteRequest Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Host Delete Verify
    public function deleteVerify(Request $request)
    {
        try {
            $host = auth()->user(); // Sanctum Auth

            if (!$host) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            $validator = Validator::make($request->all(), [
                'otp' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            if ((int)$host->otp !== (int)$request->otp) {
                return response()->json(['message' => 'OTP not matched'], 400);
            }

            $host->update([
                'account_soft_deleted' => true,
                'account_soft_deleted_at' => now(),
                'otp' => null
            ]);
//dd($host);
            return response()->json([
                'message' => 'Account soft-deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error("HostDeleteVerify Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete Host and All Related Data
    public function deleteAccount(Request $request)
    {
        try {
            $hostId = $request->id;

            // Validate host exists
            $host = Host::find($hostId);

            if (!$host) {
                return response()->json([
                    'message' => 'Host not found.'
                ], 404);
            }

            DB::transaction(function () use ($host, $hostId) {

                // Delete bookings
                Booking::where('host_id', $hostId)->delete();

                // Delete guest groups
                GuestGroup::where('host_id', $hostId)->delete();

                // Delete checklists
                Checklist::where('host_id', $hostId)->delete();

                // Delete favorites
                Favorite::where('host_id', $hostId)->delete();

                // Delete messages & chats
                $chats = Chat::where('participants->userId', $hostId)
                    ->where('participants->userModel', 'host')
                    ->get();

                $chatIds = $chats->pluck('id');

                Message::whereIn('chat_id', $chatIds)->delete();
                Chat::whereIn('id', $chatIds)->delete();

                // Finally delete host
                $host->delete();
            });

            return response()->json([
                'message' => 'Host and all related data deleted.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("DeleteHostAccount Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete host',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
