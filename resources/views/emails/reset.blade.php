<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Changed Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        h1 {
            font-size: 24px;
            color: #007c26;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            color: #333333;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin: 20px 0;
            background-color: #007c26;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            margin-top: 30px;
        }
        .footer a {
            color: #007c26;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Password Changed Successfully</h1>
    <p>Hi <strong>{{ $name }}</strong>,</p>
    <p>Your password has been updated successfully. If you did not perform this action, please contact our support team immediately.</p>

    <!-- Optional Support Button -->
    <!--
    <a href="https://www.wedbooki.com/support" class="button">Contact Support</a>
    -->

    <p>Thanks,<br>
        <strong>WED BOOKI (Pvt) Ltd</strong><br>
        <a href="https://www.wedbooki.com">WedBooki.com</a></p>
</div>

<div class="footer">
    © {{ date('Y') }} WED BOOKI (Pvt) Ltd
</div>
</body>
</html>
