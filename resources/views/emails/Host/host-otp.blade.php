<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #6366f1;
            text-align: center;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Hello {{ $name }},</h2>
    <p>Thank you for signing up! Please use the following OTP to verify your account:</p>
    <div class="otp-code">{{ $otp }}</div>
    <p>This code will expire in 10 minutes.</p>
    <p>If you didn't request this, please ignore this email.</p>
</div>
</body>
</html>
