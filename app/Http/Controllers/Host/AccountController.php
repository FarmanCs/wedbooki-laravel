<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Checklist;
use App\Models\Favourite;
use App\Models\GuestGroup;
use App\Models\Host;
use App\Models\Message;
use App\Models\Vendor\Booking;
use App\Services\EmailService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    protected $emailService;
    protected $otpService;

    public function __construct(EmailService $emailService, OtpService $otpService)
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
    }

    public function deactivateRequest(Request $request, $hostId)
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($host->account_soft_deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = $this->otpService->generateOtp();
        $host->update(['otp' => $otp]);

        $this->emailService->sendForgetPasswordOtp($host->full_name, $host->email, $otp);

        return response()->json([
            'message' => 'OTP sent to your email to confirm deactivation'
        ]);
    }

    public function deactivateVerify(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($host->account_soft_deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($host->otp != $request->otp) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $host->update([
            'account_deactivated' => true,
            'otp' => null
        ]);

        return response()->json([
            'message' => 'Account deactivated successfully'
        ]);
    }

    public function deleteRequest(Request $request, $hostId)
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = $this->otpService->generateOtp();
        $host->update(['otp' => $otp]);

        $this->emailService->sendForgetPasswordOtp($host->full_name, $host->email, $otp);

        return response()->json([
            'message' => 'OTP sent to your email to confirm account deletion'
        ]);
    }

    public function deleteVerify(Request $request, $hostId)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($host->otp != $request->otp) {
            return response()->json(['message' => 'OTP not matched'], 400);
        }

        $host->update([
            'account_soft_deleted' => true,
            'account_soft_deleted_at' => now(),
            'otp' => null
        ]);

        return response()->json([
            'message' => 'Account soft-deleted successfully'
        ]);
    }

    public function deleteAccount($hostId)
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'Host not found'], 404);
        }

        try {
            // Delete all related data
            $this->deleteHostAndAllData($hostId);

            return response()->json([
                'message' => 'Host and all related data deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete host',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function deleteHostAndAllData($hostId)
    {
        // Delete bookings
        Booking::where('host_id', $hostId)->delete();

        // Delete guest groups
        GuestGroup::where('host_id', $hostId)->delete();

        // Delete checklist
        Checklist::where('host_id', $hostId)->delete();

        // Delete favourites
        Favourite::where('host_id', $hostId)->delete();

        // Find and delete chats
        $chats = Chat::where('participants->userId', $hostId)
            ->where('participants->userModel', 'host')
            ->get();

        $chatIds = $chats->pluck('id');

        // Delete messages in those chats
        Message::whereIn('chat_id', $chatIds)->delete();

        // Delete the chats
        Chat::whereIn('id', $chatIds)->delete();

        // Finally delete the host
        Host::where('id', $hostId)->delete();
    }
}
