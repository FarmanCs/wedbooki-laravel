<!DOCTYPE html>
<html>
<head>
    <style>
        .panel {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            width: 100%;
        }
    </style>
</head>
<body>
<h1>Hello {{ ucwords($name) }}!</h1>

<p>You are receiving this email because a request was made to reset your password.</p>
<p>Use the following One-Time Password (OTP) to proceed:</p>

<table class="panel" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td>{{ $otp }}</td>
    </tr>
</table>

<p>This OTP is valid for 10 minutes.</p>

<p>If you did not request this, you can ignore this email.</p>

<p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
