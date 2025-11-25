<?php

namespace App\Src\Services;

class OtpService
{
    public function generateOtp()
    {
        return rand(1000, 9999);
    }
}
