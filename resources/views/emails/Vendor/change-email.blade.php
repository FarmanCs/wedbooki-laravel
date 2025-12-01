<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Change OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 22px;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .otp-box {
            margin: 20px 0;
            padding: 15px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            display: inline-block;
        }
        .footer {
            margin-top: 25px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hello {{ ucwords($name) }},</h1>

    <p>You requested to change your email. Use the following OTP to confirm your new email address:</p>

    <div class="otp-box">{{ $otp }}</div>

    <p>This OTP is valid for 10 minutes. If you did not request this, please ignore this email.</p>

    <p>Thanks,<br>{{ config('app.name') }}</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
