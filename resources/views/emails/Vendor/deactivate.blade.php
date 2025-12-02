<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deactivation OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f7f7f7;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .otp-box {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 6px;
            margin-top: 20px;
            letter-spacing: 4px;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Hello {{ $name }},</h2>

    <p>
        You requested to <strong>deactivate your vendor account</strong>.
        To confirm this action, please use the following OTP:
    </p>

    <div class="otp-box">
        {{ $otp }}
    </div>

    <p>If you did not request this, please ignore this email.</p>

    <p>Regards,<br>
        <strong>{{ config('app.name') }}</strong></p>
</div>

<div class="footer">
    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</div>

</body>
</html>
