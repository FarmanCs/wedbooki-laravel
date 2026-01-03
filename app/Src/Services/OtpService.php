<?php

namespace App\Src\Services;

use App\Mail\Host\HostOtpMail;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public static function generateOtp()
    {
        return rand(100000, 999999);
    }

    public static function sendOtp(string $email, string $otp, string $name): void
    {
        Mail::to($email)->send(new HostOtpMail($otp, $name));
    }
}
