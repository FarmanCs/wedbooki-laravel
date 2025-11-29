<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Timing;
use App\Models\Vendor\Booking;
use App\Src\Services\BookingService;
use App\Src\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    protected $bookingService;
    protected $emailService;

    public function __construct(BookingService $bookingService, EmailService $emailService)
    {
        $this->bookingService = $bookingService;
        $this->emailService = $emailService;
    }

    // Create a venue booking
    public function createVenueBooking(Request $request)
    {
        try {
            // AUTH: Sanctum user
            $host = auth()->user();
            if (!$host || $host->role !== 'host') {
                return response()->json([
                    'message' => 'Only hosts can create bookings.'
                ], 403);
            }

            // VALIDATION
            $validator = Validator::make($request->all(), [
                'package_id' => 'nullable|exists:packages,id',
                'business_id' => 'required_without:package_id|exists:businesses,id',
                'event_date' => 'required|string',
                'time_slot' => 'required|string',
                'timezone' => 'required|string|timezone',
                'guests' => 'nullable|integer|min:1',
                'extra_services' => 'nullable|array',
                'extra_services.*' => 'exists:services,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            // Convert request → array and match express.js names
            $data = [
                'package_id' => $request->package_id,
                'business_id' => $request->business_id,
                'event_date' => $request->event_date,
                'time_slot' => $request->time_slot,
                'timezone' => $request->timezone,
                'guests' => $request->guests,
                'extra_services' => $request->extra_services,
            ];

            // IMPORTANT EXACT EXPRESS CHECK:
            if (
                empty($data['package_id']) &&
                (empty($data['extra_services']) ||
                    !is_array($data['extra_services']) ||
                    count($data['extra_services']) === 0)
            ) {
                return response()->json([
                    'message' =>
                        'Either package_id or non-empty extra_services must be provided for venue bookings.',
                ], 400);
            }

            // CALL BOOKING SERVICE (Same as Express logic)
            $result = $this->bookingService->createVenueBooking($host->id, $data);

            return response()->json($result, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Create a vendor booking (non-venue)
    public function createBooking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'package_id' => 'nullable|exists:packages,id',
            'event_date' => ['required',
                Rule::date()->afterOrEqual(today()->addDays(7)),
                'date_format:d-m-Y'
            ],
            'start_time' =>'required|date_format:h:i A',
            'end_time'=>'required|date_format:h:i A|after:start_time',
            'timezone'=>'required|timezone',
            'extra_services'=>'required_unless:package_id,null|array',
        ]);
        if($validation->fails()){
            return response()->json([$validation->errors()], 400);
        }
//        dd($validation->getData());

        try {
            $hostId = auth()->id();
            $data = $validation->getData();
            $data['host_id'] = $hostId;

            $booking = $this->bookingService->createBooking($data);

            return response()->json([
                'message' => 'Vendor booked successfully.',
                'booking' => $booking,
                'booking_id' => $booking->custom_booking_id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
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
    public function rejectVenueBooking(Request $request, $bookingId)
    {
        try {
            $booking = Booking::with(['business', 'host', 'vendor'])->find($bookingId);

            if (!$booking) {
                return response()->json(['message' => 'Booking not found.'], 404);
            }

            // Update timings to mark slot as active
            $timings = Timing::where('business_id', $booking->business->id)->first();
            $updatedStatus = null;

            if ($timings) {
                $localMoment = Carbon::parse($booking->event_date);
                $dayOfWeek = strtolower($localMoment->format('l'));

                if (
                    isset($timings->timings_venue[$dayOfWeek]) &&
                    isset($timings->timings_venue[$dayOfWeek][$booking->time_slot])
                ) {
                    $timings->timings_venue[$dayOfWeek][$booking->time_slot]['status'] = 'active';
                    $timings->save();
                    $updatedStatus = $timings->timings_venue[$dayOfWeek][$booking->time_slot]['status'];
                }
            }

            // Update booking status
            $booking->status = 'rejected';
            $booking->save();

            // Send emails
            try {
                $fullTimeSlot = $booking->event_date . ' from ' .
                    Carbon::parse($booking->start_time)->format('H:i') . ' to ' .
                    Carbon::parse($booking->end_time)->format('H:i') .
                    ' (' . $booking->timezone . ')';

                // Host Email
                if ($booking->host && $booking->host->email) {
                    $hostEmailBody = $this->emailService->hostBookingCancelTemplate([
                        'hostName' => $booking->host->full_name ?? $booking->host->email ?? 'Host',
                        'serviceName' => $booking->vendor->company_name ?? $booking->vendor->full_name ?? $booking->business->company_name ?? 'Service',
                        'timeDetails' => $fullTimeSlot
                    ]);
                    $this->emailService->sendEmail($booking->host->email, $hostEmailBody);
                }

                // Vendor Email
                if ($booking->vendor && $booking->vendor->email) {
                    $vendorEmailBody = $this->emailService->vendorBookingCancelTemplate([
                        'vendorName' => $booking->vendor->full_name ?? $booking->vendor->company_name ?? 'Vendor',
                        'vendorCompany' => $booking->vendor->company_name ?? $booking->business->company_name ?? 'Vendor',
                        'timeDetails' => $fullTimeSlot,
                        'hostName' => $booking->host->full_name ?? 'Host'
                    ]);
                    $this->emailService->sendEmail($booking->vendor->email, $vendorEmailBody);
                }
            } catch (Exception $e) {
                \Log::warning("Failed to send rejection emails: " . $e->getMessage());
            }

            return response()->json([
                'message' => 'Booking rejected successfully.',
                'bookingId' => $bookingId,
                'slotStatus' => $updatedStatus,
            ], 200);

        } catch (Exception $err) {
            \Log::error("RejectVenueBooking Error: " . $err->getMessage());
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $err->getMessage()
            ], 500);
        }
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
