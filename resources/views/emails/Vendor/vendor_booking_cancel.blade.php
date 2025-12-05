<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Cancelled</title>
</head>
<body style="font-family: Arial, sans-serif;">

<h2>Hello {{ $booking->vendor->full_name ?? 'Vendor' }},</h2>

<p>
    A booking for your service/venue
    <strong>{{ $booking->business->company_name ?? 'your business' }}</strong>
    has been <strong>cancelled/rejected</strong>.
</p>

<p>
    <strong>Time Slot:</strong><br>
    {{ $timeDetails['formatted_event_date'] ?? '' }} <br>
    From {{ $timeDetails['formatted_start_time'] ?? '' }}
    to {{ $timeDetails['formatted_end_time'] ?? '' }} ({{ $booking->timezone ?? '' }})
</p>

<p>
    <strong>Booked By:</strong> {{ $booking->host->full_name ?? 'Host' }}<br>
    <strong>Email:</strong> {{ $booking->host->email ?? 'N/A' }}
</p>

<p>Please adjust your schedule accordingly. Thank you!</p>

<p>Regards,<br>Team Booking Management</p>

</body>
</html>
