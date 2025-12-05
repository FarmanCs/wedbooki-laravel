<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Booking Accepted</title>
</head>

<body style="font-family: Arial, sans-serif;">

<h2>Hello {{ $booking->vendor->full_name ?? 'Vendor' }},</h2>

<p>
    A booking for your business
    <strong>{{ $booking->business->company_name ?? 'your business' }}</strong>
    has been <strong>accepted</strong>.
</p>

<p>
    <strong>Time Slot:</strong><br>
    {{ $timeDetails['formatted_event_date'] }} <br>
    From {{ $timeDetails['formatted_start_time'] }}
    to {{ $timeDetails['formatted_end_time'] }} ({{ $booking->timezone }})
</p>

<p>
    <strong>Booked By:</strong> {{ $booking->host->full_name ?? 'N/A' }}<br>
    <strong>Email:</strong> {{ $booking->host->email ?? 'N/A' }}
</p>

<p>
    Please prepare accordingly for the event. Thank you!
</p>

<p>Regards,<br>Team Booking Management</p>

</body>
</html>
