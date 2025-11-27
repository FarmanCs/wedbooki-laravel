<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Timing;
use App\Models\Vendor\Booking;
use App\Src\Services\BookingService;
use App\Src\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $bookingService;
    protected $emailService;

    public function __construct(BookingService $bookingService, EmailService $emailService)
    {
        $this->bookingService = $bookingService;
        $this->emailService = $emailService;
    }

    /**
     * Create a venue booking
     * Uses authenticated user from Sanctum
     */
    public function createVenueBooking(Request $request)
    {
        // Get authenticated host from Sanctum
        $host = auth()->user();

        if (!$host || $host->role !== 'host') {
            return response()->json([
                'message' => 'Only hosts can create bookings.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => 'nullable|exists:packages,id',
            'business_id' => 'required_without:package_id|exists:businesses,id',
            'event_date' => 'required|date_format:d-m-Y',
            'time_slot' => 'required|string',
            'timezone' => 'required|string|timezone',
            'guests' => 'nullable|integer|min:1',
            'extra_services' => 'nullable|array',
            'extra_services.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->bookingService->createVenueBooking($host->id, $request->all());
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a vendor booking (non-venue)
     */
    public function createVendorBooking(Request $request)
    {
        $host = auth()->user();

        if (!$host || $host->role !== 'host') {
            return response()->json([
                'message' => 'Only hosts can create bookings.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => 'nullable|exists:packages,id',
            'business_id' => 'required_without:package_id|exists:businesses,id',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'timezone' => 'required|string|timezone',
            'extra_services' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->bookingService->createVendorBooking($host->id, $request->all());
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all bookings for authenticated host
     */
    public function getAllBookings(Request $request)
    {
        $host = auth()->user();

        $bookings = Booking::with(['business.category', 'package', 'vendor'])
            ->where('host_id', $host->id)
            ->orderBy('event_date', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'No bookings found'
            ], 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return $this->bookingService->formatBookingTime($booking);
        });

        return response()->json([
            'message' => 'Found bookings',
            'bookings' => $formattedBookings
        ]);
    }

    /**
     * Get a specific booking by ID
     */
    public function getBookingById($bookingId)
    {
        $host = auth()->user();

        $booking = Booking::with(['business.category', 'package', 'vendor'])
            ->where('id', $bookingId)
            ->where('host_id', $host->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'message' => 'Booking fetched successfully',
            'data' => $booking
        ]);
    }


    //get vendor timings
    public function vendorTimings(Request $request, $business_id)
    {
        try {
            // Fetch timings by business ID
            $timings = Timing::where('business_id', $business_id)->first();

            if (!$timings) {
                return response()->json([
                    'message' => 'Vendor timings not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Unavailable dates fetched successfully',
                'timings' => $timings
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Error fetching unavailable dates: ' . $e->getMessage());

            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }


    //rject rejectVenueBooking
    public function rejectVenueBooking(){

    }


    public function cancelVenueBooking()
    {

    }
    /**
     * Cancel a booking
     */
    public function cancelBooking(Request $request)
    {
        $host = auth()->user();

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->bookingService->cancelBooking($host->id, $request->booking_id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available slots for a vendor
     */
    public function getVendorAvailableSlots($vendorId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'timezone' => 'required|string|timezone'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $slots = $this->bookingService->getVendorAvailableSlots(
                $vendorId,
                $request->date,
                $request->timezone
            );
            return response()->json($slots);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get slots for a specific date
     */
    public function getSlotsForDate($vendorId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'timezone' => 'required|string|timezone'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $slots = $this->bookingService->getSlotsForDate(
                $vendorId,
                $request->date,
                $request->timezone
            );
            return response()->json($slots);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
