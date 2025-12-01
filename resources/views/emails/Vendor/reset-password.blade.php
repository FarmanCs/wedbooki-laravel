<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        h1 {
            color: #333333;
            font-size: 24px;
        }
        p {
            font-size: 16px;
            color: #555555;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hello {{ ucwords($name) }},</h1>

    <p>Your password has been successfully reset.</p>
    <p>If you did not perform this action, please contact our support immediately.</p>

    <p>Thanks,<br>{{ config('app.name') }}</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
