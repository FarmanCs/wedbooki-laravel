<?php

namespace App\Src\Services\Vendor;

use App\Mail\Host\HostBookingCancelMail;
use App\Mail\Host\HostBookingMail;
use App\Mail\Vendor\VendorBookingCancelMail;
use App\Mail\Vendor\VendorBookingMail;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class VendorBookingService
{

    public function getVendorBookings($businessId): JsonResponse
    {
        // Check your hosts table structure and only select existing columns
        $bookings = Booking::select(['id', 'host_id', 'package_id', 'business_id'])->with([
            'host:id,email,full_name,country,phone_no,category', // Removed 'phone' since it doesn't exist
            'package:id,name,price,discount,discount_percentage',
            'business:id,business_email',
            'business.extraServices:id,business_id,name,price'
        ])
            ->where('business_id', $businessId)
            ->paginate(2);
//dd($bookings->toArray());
        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found.'], 404);
        }

        return response()->json([
            'message' => 'Bookings found',
            'bookings' => $bookings->items(),
            "total_records" => $bookings->total()
        ], 200);
    }

    public function vendorSingleBooking($bookingId): JsonResponse
    {
        $booking = Booking::with('host:id,full_name,email,phone_no,profile_image')
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
