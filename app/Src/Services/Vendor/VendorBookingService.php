<?php

namespace App\Src\Services\Vendor;

use App\Mail\HostBookingCancelMail;
use App\Mail\HostBookingMail;
use App\Mail\VendorBookingCancelMail;
use App\Mail\VendorBookingMail;
use App\Models\Host;
use App\Models\Vendor\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class VendorBookingService
{
    public function getVendorBookings($businessId): JsonResponse
    {
        $bookings = Booking::where('business_id', $businessId);

//        dd($bookings);


        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found.'], 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                '_id' => $booking->id,
                'host' => $booking->host,
                'package' => $booking->package,
                'custom_booking_id' => $booking->custom_booking_id,
                'amount' => $booking->amount,
                'extra_services' => $booking->extra_services,
                'payment_status' => $booking->payment_status,
                'status' => $booking->status,
                'start_time' => Carbon::parse($booking->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($booking->end_time)->format('h:i A'),
                'date' => Carbon::parse($booking->event_date)->format('d M Y'),
                'time_range' => Carbon::parse($booking->start_time)->format('h:i A') . ' - ' . Carbon::parse($booking->end_time)->format('h:i A'),
                'createdAt' => $booking->created_at
            ];
        });

        return response()->json([
            'message' => 'Bookings found',
            'bookings' => $formattedBookings
        ], 200);
    }

    public function vendorSingleBooking($bookingId): JsonResponse
    {
        $booking = Booking::with('host:id,full_name,email,profile_image')
            ->find($bookingId);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        return response()->json([
            'message' => 'Booking found',
            'booking' => $booking
        ], 200);
    }

    public function acceptBooking($hostId, array $data): JsonResponse
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'Host not found.'], 404);
        }

        $booking = Booking::with(['package', 'venue', 'host', 'business'])
            ->find($data['bookingId']);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        $booking->status = 'accepted';
        $booking->approved_at = now();
        $booking->save();

        // Send emails
        $this->sendBookingEmails($booking, 'accepted');

        // Create checklist items for host
        $this->createPaymentChecklist($booking);

        return response()->json(['message' => 'Booking accepted successfully.'], 200);
    }

    public function rejectBooking($hostId, array $data): JsonResponse
    {
        $host = Host::find($hostId);

        if (!$host) {
            return response()->json(['message' => 'Host not found.'], 404);
        }

        $booking = Booking::with(['package', 'venue', 'host', 'business'])
            ->find($data['bookingId']);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        $booking->status = 'rejected';
        $booking->save();

        // Free the slot
        $this->freeBookingSlot($booking);

        // Send emails
        $this->sendBookingEmails($booking, 'rejected');

        return response()->json(['message' => 'Booking rejected successfully and slot freed.'], 200);
    }

    private function sendBookingEmails($booking, $status)
    {
        $timeDetails = $this->formatBookingTime($booking);

        if ($status === 'accepted') {
            if ($booking->host && $booking->host->email) {
                Mail::to($booking->host->email)->send(new HostBookingMail($booking, $timeDetails));
            }
            if ($booking->venue && $booking->venue->email) {
                Mail::to($booking->venue->email)->send(new VendorBookingMail($booking, $timeDetails));
            }
        } else {
            if ($booking->host && $booking->host->email) {
                Mail::to($booking->host->email)->send(new HostBookingCancelMail($booking, $timeDetails));
            }
            if ($booking->venue && $booking->venue->email) {
                Mail::to($booking->venue->email)->send(new VendorBookingCancelMail($booking, $timeDetails));
            }
        }
    }

    private function formatBookingTime($booking)
    {
        $start = Carbon::parse($booking->start_time)->setTimezone($booking->timezone);
        $end = Carbon::parse($booking->end_time)->setTimezone($booking->timezone);
        $date = Carbon::parse($booking->event_date)->setTimezone($booking->timezone);

        return [
            'formatted_start_time' => $start->format('h:i A'),
            'formatted_end_time' => $end->format('h:i A'),
            'formatted_event_date' => $date->format('d M Y'),
            'formatted_range' => $start->format('h:i A') . ' - ' . $end->format('h:i A')
        ];
    }

    private function freeBookingSlot($booking)
    {
        // Implementation to mark slot as available again
    }

    private function createPaymentChecklist($booking)
    {
        // Implementation to create payment checklist items for host
    }
}
