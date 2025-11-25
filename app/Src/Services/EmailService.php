<?php

namespace App\Src\Services;

use App\Mail\EmailChangeMail;
use App\Mail\ForgetPasswordOtp;
use App\Mail\ResetPasswordMail;
use App\Mail\SignupOtp;
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

    public function sendEmailChangeOtp($user, $otp)
    {
        Mail::to($user->email)->send(new EmailChangeMail(
            $user->full_name ?? $user->name,
            $otp,
        ));

    }

}
