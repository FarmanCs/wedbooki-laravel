<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deletion OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f5f5f5;
            padding: 30px;
            color: #333;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }
        h2 {
            color: #d9534f;
        }
        .otp-box {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 18px;
            background: #f0f0f0;
            border-radius: 6px;
            margin: 25px 0;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Hello {{ $name }},</h2>

    <p>You have requested to <strong>delete your vendor account permanently.</strong></p>

    <p>Please use the following OTP to confirm deletion:</p>

    <div class="otp-box">
        {{ $otp }}
    </div>

    <p><strong>Warning:</strong> This action is <u>irreversible</u>.
        All your data will be permanently removed.</p>

    <p>If you did NOT request this, please ignore this email or contact support.</p>

    <p>Regards,<br>
        <strong>{{ config('app.name') }}</strong></p>
</div>

<div class="footer">
    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</div>

</body>
</html>
