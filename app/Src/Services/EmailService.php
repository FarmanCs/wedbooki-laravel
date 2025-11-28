<?php

namespace App\Src\Services;

use App\Mail\Host\AccountDeactivationOtp;
use App\Mail\Vendor\HostBookingMail;
use App\Mail\Vendor\VendorBookingMail;
use App\Mail\EmailChangeMail;
use App\Mail\ForgetPasswordOtp;
use App\Mail\ResetPasswordMail;
use App\Mail\SignupOtp;
use App\Mail\Host\PasswordUpdateMail;
use App\Mail\Host\AccountDeletionOtp;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send signup OTP email
     */
    public function sendSignupOtp($user, $otp)
    {
        Mail::to($user->email)->send(new SignupOtp($user->full_name, $otp));
    }

    /**
     * Send Forget Password OTP
     */
    public function sendForgetPasswordOtp($user, $otp)
    {
        Mail::to($user->email)->send(new ForgetPasswordOtp($user->full_name, $otp));
    }

    /**
     * Password Reset Confirmation
     */
    public function sendPasswordResetConfirmation($host)
    {
        Mail::to($host->email)->send(new ResetPasswordMail($host->full_name));
    }

    /**
     * Send Email Change OTP
     */
    public function sendEmailChangeOtp($user, $otp)
    {
        Mail::to($user->email)->send(new EmailChangeMail(
            $user->full_name ?? $user->name,
            $otp
        ));
    }

    /**
     * Send booking email to host
     */
    public function sendHostBookingEmail($host, $business, $formattedTime)
    {
        Mail::to($host->email)->send(
            new HostBookingMail($host, $business, $formattedTime)
        );
    }

    /**
     * Send booking email to vendor
     */
    public function sendVendorBookingEmail($vendor, $business, $formattedTime, $hostName)
    {
        Mail::to($vendor->email)->send(
            new VendorBookingMail($vendor, $business, $formattedTime, $hostName)
        );
    }

    /**
     * Send password update confirmation email
     */
    public function sendPasswordUpdateConfirmation($user)
    {
        Mail::to($user->email)->send(new PasswordUpdateMail($user->full_name ?? $user->name));
    }

    public function sendAccountDeactivationOtp($host, $otp)
    {
        Mail::to($host->email)->send(new AccountDeactivationOtp($host->full_name, $otp));
    }

    public function sendAccountDeletionOtp($user, $otp)
    {
        Mail::to($user->email)->send(new AccountDeletionOtp($user->full_name ?? $user->name, $otp
        ));
    }
}
