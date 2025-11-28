<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deactivation OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow-lg">
    <p class="text-gray-700 text-lg mb-4">Hi <span class="font-semibold">{{ $fullName }}</span>,</p>

    <p class="text-gray-700 mb-6">
        You requested to deactivate your account. Use the following OTP to confirm:
    </p>

    <div class="text-center mb-6">
        <h2 class="text-3xl font-bold text-red-600 py-4 px-6 border-2 border-red-600 rounded-lg inline-block">
            {{ $otp }}
        </h2>
    </div>

    <p class="text-gray-500 text-sm">
        If you did not request this, please ignore this email.
    </p>
</div>
</body>
</html>

