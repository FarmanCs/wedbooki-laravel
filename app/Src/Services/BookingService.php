<?php

namespace App\Src\Services;

use App\Models\Host\Host;
use App\Models\Vendor\Timing;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Package;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BookingService
{
    protected $emailService;
    protected $counterService;
    protected $timeService;

    public function __construct(
        EmailService $emailService,
        CounterService $counterService,
        TimeService $timeService
    ) {
        $this->emailService = $emailService;
        $this->counterService = $counterService;
        $this->timeService = $timeService;
    }

    public function createVenueBooking($hostId, array $data)
    {
        return DB::transaction(function () use ($hostId, $data) {

            // Validate host
            $host = Host::find($hostId);
            if (!$host || $host->role !== 'host') {
                throw new Exception('Only hosts can create bookings.');
            }

            if (empty($data['event_date']) || empty($data['timezone']) || empty($data['time_slot'])) {
                throw new Exception('event_date, timezone, and time_slot are required.');
            }

            if (empty($data['package_id']) &&
                (empty($data['extra_services']) || !is_array($data['extra_services']) || count($data['extra_services']) === 0)) {
                throw new Exception('Either package_id or extra services must be provided.');
            }

            $selectedPackage = null;

            // Fetch business with category
            if (!empty($data['package_id'])) {
                $selectedPackage = Package::find($data['package_id']);
                if (!$selectedPackage) throw new Exception('Package not found.');

                $business = Business::with('category')->find($selectedPackage->business_id);
            } else {
                if (empty($data['business_id'])) throw new Exception('business_id is required.');

                $business = Business::with('category')->find($data['business_id']);
            }

            if (!$business) throw new Exception('Business not found.');

            $vendor = Vendor::where('business_id', $business->id)->first();
            if (!$vendor) throw new Exception('Vendor not found.');

            // Timing
            $timings = Timing::where('business_id', $business->id)->first();
            if (!$timings || empty($timings->timings_venue)) {
                throw new Exception('No timings found for this venue.');
            }

            // Date + Time Handling
            $localMoment = Carbon::createFromFormat('d-m-Y', $data['event_date'], $data['timezone'])->setTime(12, 0);
            $dayOfWeek = strtolower($localMoment->format('l'));
            $localDate = $localMoment->format('d-m-Y');

            $dayTimings = $timings->timings_venue[$dayOfWeek] ?? null;
            $slotTiming = $dayTimings[$data['time_slot']] ?? null;

            if (!$slotTiming || $slotTiming['status'] !== 'active') {
                throw new Exception("The selected time slot is not available on {$dayOfWeek}.");
            }

            if (is_array($timings->unavailable_dates) && in_array($localDate, $timings->unavailable_dates)) {
                throw new Exception('The venue is unavailable on the selected date.');
            }

            // Check if already booked
            $existingBooking = Booking::where('business_id', $business->id)
                ->whereDate('event_date', $localMoment->toDateString())
                ->where('time_slot', $data['time_slot'])
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->first();

            if ($existingBooking) {
                throw new Exception('Venue already booked for this time slot.');
            }

            // Extra services
            $selectedExtras = $data['extra_services'] ?? [];

            // Slot Time Format
            $formattedTime = $this->timeService->formatSlotTime(
                $slotTiming,
                $localDate,
                $data['time_slot'],
                $data['timezone']
            );

            // Booking ID
            $customBookingId = $this->counterService->getNextCounter('id', 'WB-B400');

            // Prices
            $packagePrice = $selectedPackage->price ?? 0;
            $discountAmount = $selectedPackage->discount ?? 0;
            $extrasTotal = array_sum(array_column($selectedExtras, 'price'));

            $finalBase = $discountAmount > 0 ? $discountAmount : $packagePrice;
            $totalAmount = $finalBase + $extrasTotal;

            $priceBreakdown = [
                'basePrice' => $packagePrice,
                'discount' => $discountAmount,
                'extras' => $extrasTotal,
                'finalPrice' => $totalAmount,
            ];

            // Payment Calculations
            $advancePercentage = $business->advance_percentage ?? 10;
            $advanceAmount = round(($totalAmount * $advancePercentage) / 100, 2);
            $finalAmount = $totalAmount - $advanceAmount;

            $advanceDue = Carbon::now()->addDays($business->payment_days_advance ?? 7);
            $finalDue = $localMoment->copy()->subDays($business->payment_days_final ?? 1);

            if ($advanceDue->isAfter($finalDue)) {
                $advanceDue = $finalDue->copy();
            }

            // Create Booking
            $booking = Booking::create([
                'host_id' => $hostId,
                'business_id' => $business->id,
                'vendor_id' => $vendor->id,
                'package_id' => $selectedPackage->id ?? null,
                'amount' => $totalAmount,
                'event_date' => $localMoment->toDateString(),
                'time_slot' => $data['time_slot'],
                'custom_booking_id' => $customBookingId,
                'timezone' => $data['timezone'],
                'guests' => $data['guests'] ?? 0,
                'start_time' => $formattedTime['start_time'],
                'end_time' => $formattedTime['end_time'],
                'extra_services' => json_encode($selectedExtras), // FIXED
                'advance_percentage' => $advancePercentage,
                'advance_amount' => $advanceAmount,
                'final_amount' => $finalAmount,
                'advance_due_date' => $advanceDue->toDateString(),
                'final_due_date' => $finalDue->toDateString(),
                'status' => 'pending',
            ]);

            // Update Timings
            $timingsVenue = $timings->timings_venue;
            $timingsVenue[$dayOfWeek][$data['time_slot']]['status'] = 'booked';
            $timings->timings_venue = $timingsVenue;
            $timings->save();

            // If all slots filled → mark day unavailable
            $bookedSlots = Booking::where('business_id', $business->id)
                ->whereDate('event_date', $localMoment->toDateString())
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->pluck('time_slot')->toArray();

            $allSlots = array_keys($dayTimings);
            if (empty(array_diff($allSlots, $bookedSlots))) {
                $unavailableDates = $timings->unavailable_dates ?? [];
                if (!in_array($localDate, $unavailableDates)) {
                    $unavailableDates[] = $localDate;
                    $timings->unavailable_dates = $unavailableDates;
                    $timings->save();
                }
            }

            // Send Emails
            $this->emailService->sendHostBookingEmail($host, $business, $formattedTime['formatted']);
            $this->emailService->sendVendorBookingEmail($vendor, $business, $formattedTime['formatted'], $host->full_name);

            // Auto-accept
            try {
                Http::put(config('app.api_url') . "/api/v1/vendor/accept-booking/{$hostId}", [
                    'bookingId' => $booking->id,
                ]);
            } catch (Exception $e) {
                \Log::warning("Auto accept failed: ".$e->getMessage());
            }

            return [
                'message' => 'Venue booked successfully.',
                'booking' => $booking->load(['business','package','vendor']), // works after relationships
                'bookingId' => $booking->custom_booking_id,
                'priceBreakdown' => $priceBreakdown,
            ];
        });
    }

    public function cancelBooking($hostId, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)->where('host_id', $hostId)->first();
        if (!$booking) {
            throw new Exception('Booking not found.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return [
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking,
        ];
    }

    public function formatBookingTime($booking)
    {
        // Format the event date nicely
        $formattedDate = \Carbon\Carbon::parse($booking->event_date)->format('d-m-Y');

        // Format start and end time
        $startTime = \Carbon\Carbon::parse($booking->start_time)->format('H:i');
        $endTime = \Carbon\Carbon::parse($booking->end_time)->format('H:i');

        // Prepare extra services if any
        $extraServices = [];
        if (!empty($booking->extra_services) && is_array($booking->extra_services)) {
            $extraServices = array_map(function ($service) {
                return [
                    'id' => $service['id'] ?? null,
                    'name' => $service['name'] ?? null,
                    'price' => $service['price'] ?? 0,
                ];
            }, $booking->extra_services);
        }

        return [
            'booking_id' => $booking->custom_booking_id,
            'host_id' => $booking->host_id,
            'vendor_id' => $booking->vendor_id,
            'business' => [
                'id' => $booking->business->id ?? null,
                'name' => $booking->business->company_name ?? null,
                'category' => $booking->business->category->name ?? null,
                'type' => $booking->business->category->type ?? null,
            ],
            'package' => $booking->package ? [
                'id' => $booking->package->id,
                'name' => $booking->package->name,
                'price' => $booking->package->price,
                'discount' => $booking->package->discount,
            ] : null,
            'amount' => $booking->amount,
            'advance_amount' => $booking->advance_amount,
            'final_amount' => $booking->final_amount,
            'advance_due_date' => $booking->advance_due_date,
            'final_due_date' => $booking->final_due_date,
            'event_date' => $formattedDate,
            'time_slot' => $booking->time_slot,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'guests' => $booking->guests,
            'extra_services' => $extraServices,
            'status' => $booking->status,
            'timezone' => $booking->timezone,
            'created_at' => $booking->created_at->format('d-m-Y H:i'),
            'updated_at' => $booking->updated_at->format('d-m-Y H:i'),
        ];
    }

}
