<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333333;
        }
        p {
            color: #555555;
            line-height: 1.5;
        }
        .details {
            margin-top: 20px;
        }
        .details th, .details td {
            text-align: left;
            padding: 8px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Booking Confirmation</h2>
    <p>Dear {{ $host->full_name }},</p>
    <p>Your booking has been successfully created. Below are the details:</p>

    <table class="details">
        <tr>
            <th>Business Name:</th>
            <td>{{ $business->company_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Event Date:</th>
            <td>{{ $formattedTime['date'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Time Slot:</th>
            <td>{{ $formattedTime['slot'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Start Time:</th>
            <td>{{ $formattedTime['start_time'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>End Time:</th>
            <td>{{ $formattedTime['end_time'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Guests:</th>
            <td>{{ $host->guests ?? 'N/A' }}</td>
        </tr>
    </table>

    <p>Thank you for choosing our services. We look forward to hosting your event!</p>

    <div class="footer">
        &copy; {{ date('Y') }} Your Company Name. All rights reserved.
    </div>
</div>
</body>
</html>
