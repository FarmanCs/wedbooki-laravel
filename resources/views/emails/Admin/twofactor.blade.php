<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Two-Factor Code</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
<div style="max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 8px;">
    <h2 style="text-align: center; color: #333;">Two-Factor Authentication</h2>

    <p>Hello <strong>{{ $name }}</strong>,</p>

    <p>Your verification code is:</p>

    <h1 style="text-align: center; background:#222; color:#fff; padding: 10px; border-radius: 5px;">
        {{ $code }}
    </h1>

    <p>This code will expire in <strong>10 minutes</strong>.</p>

    <p>Regards,<br>Wedbooki Admin</p>
</div>
</body>
</html>
