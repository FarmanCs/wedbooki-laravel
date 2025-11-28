<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deletion OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
        }
        .otp {
            color: #d9534f;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<h2>Hello {{ $fullName }},</h2>

<p>We received a request to delete your account. To confirm this action, please use the OTP below:</p>

<p class="otp">{{ $otp }}</p>

<p>This OTP is valid for the next 10 minutes. If you did not request account deletion, please ignore this email.</p>

<p>Thank you,<br>
    <strong>Your Company Name</strong></p>
</body>
</html>
