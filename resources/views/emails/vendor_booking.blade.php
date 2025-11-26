<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <title>New Booking Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; color: #333; }
        .container { max-width: 700px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0px 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .header h1 { margin: 0; color: #2c3e50; }
        .details { margin-top: 20px; }
        .details p { margin: 8px 0; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #777; text-align: center; }
        .highlight { color: #2c3e50; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>New Booking Received</h1>
    </div>

    ```
    <p>Dear <span class="highlight">{{ $vendor->full_name }}</span>,</p>

    <p>You have received a new booking for your venue:</p>

    <div class="details">
        <p><strong>Business:</strong> {{ $business->company_name }}</p>
        <p><strong>Host Name:</strong> {{ $hostName }}</p>
        <p><strong>Event Date:</strong> {{ $formattedTime['date'] ?? 'N/A' }}</p>
        <p><strong>Time Slot:</strong> {{ $formattedTime['start_time'] ?? 'N/A' }} - {{ $formattedTime['end_time'] ?? 'N/A' }}</p>
        <p><strong>Guests:</strong> {{ $formattedTime['guests'] ?? 'N/A' }}</p>
        <p><strong>Booking ID:</strong> {{ $formattedTime['booking_id'] ?? 'N/A' }}</p>
    </div>

    <p>Please make necessary preparations for the event and contact the host if needed.</p>

    <div class="footer">
        <p>Thank you for using our platform. We wish the event to be successful!</p>
    </div>
    ```

</div>
</body>
</html>
