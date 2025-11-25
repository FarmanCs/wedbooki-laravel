<?php

namespace App\Src\Services;

use App\Models\Host\Host;
use App\Models\Timings;
use App\Models\Vendor;
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

            // Validate required fields
            if (empty($data['event_date']) || empty($data['timezone']) || empty($data['time_slot'])) {
                throw new Exception('event_date, timezone, and time_slot are required for venue bookings.');
            }

            // Validate package or extra services
            if (empty($data['package_id']) &&
                (empty($data['extra_services']) || !is_array($data['extra_services']) || count($data['extra_services']) === 0)) {
                throw new Exception('Either package_id or non-empty extra_services must be provided for venue bookings.');
            }

            $selectedPackage = null;
            $business = null;

            // Get package and business
            if (!empty($data['package_id'])) {
                // If using MongoDB, find by _id string
                $selectedPackage = Package::where('id', $data['package_id'])->first();
                if (!$selectedPackage) {
                    throw new Exception('Package not found.');
                }

                $business = Business::with('category')
                    ->where('_id', $selectedPackage->business_id)
                    ->first();
            } else {
                if (empty($data['business_id'])) {
                    throw new Exception('business_id is required when booking without a package.');
                }

                $business = Business::with('category')
                    ->where('_id', $data['business_id'])
                    ->first();
            }

            if (!$business) {
                throw new Exception('Business not found.');
            }

            // Get vendor
            $vendor = Vendor::where('business_profile_id', $business->id)->first();
            if (!$vendor) {
                throw new Exception('Vendor not found.');
            }

            // Validate venue type
            if (!$business || $business->category->type !== 'venue') {
                throw new Exception('Invalid or non-venue vendor.');
            }

            // Get timings
            $timings = Timings::where('business_id', $business->id)->first();
            if (!$timings || empty($timings->timings_venue)) {
                throw new Exception('No timings found for this venue.');
            }

            // Convert to UTC and get local date
            $utcDateTime = $this->timeService->getUTCFromLocal(
                $data['event_date'],
                $data['time_slot'],
                $data['timezone']
            );

            $localMoment = Carbon::createFromFormat('d-m-Y', $data['event_date'], $data['timezone'])->setTime(12, 0);
            $localDate = $localMoment->format('d-m-Y');
            $dayOfWeek = strtolower($localMoment->format('l'));

            // Validate time slot
            $dayTimings = $timings->timings_venue[$dayOfWeek] ?? null;
            $slotTiming = $dayTimings[$data['time_slot']] ?? null;

            if (!$slotTiming || $slotTiming['status'] !== 'active') {
                throw new Exception("The selected time slot is not available on {$dayOfWeek}.");
            }

            // Check unavailable dates
            if (is_array($timings->unavailable_dates) && in_array($localDate, $timings->unavailable_dates)) {
                throw new Exception('The venue is unavailable on the selected date.');
            }

            // Check existing booking
            $existingBooking = Booking::where('business_id', $business->id)
                ->whereDate('event_date', $localMoment->toDateString())
                ->where('time_slot', $data['time_slot'])
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->first();

            if ($existingBooking) {
                throw new Exception('Venue already booked for this time slot.');
            }

            // Process extra services (commented in original)
            $selectedExtras = [];

            // Format slot time
            $formattedTime = $this->timeService->formatSlotTime(
                $slotTiming,
                $localDate,
                $data['time_slot'],
                $data['timezone']
            );

            // Generate custom booking ID
            $customBookingId = $this->counterService->getNextCounter('venue_booking_id', 'WB-B400');

            // Calculate prices
            $packagePrice = $selectedPackage->price ?? 0;
            $discountedPrice = $selectedPackage->discount ?? 0;
            $extrasTotal = array_reduce($selectedExtras, fn($sum, $item) => $sum + ($item['price'] ?? 0), 0);

            $base = $discountedPrice > 0 ? $discountedPrice : $packagePrice;
            $totalAmount = $base + $extrasTotal;

            $priceBreakdown = [
                'basePrice' => $packagePrice,
                'extras' => $extrasTotal,
                'discountedPrice' => $discountedPrice,
                'finalPrice' => $totalAmount,
            ];

            // Calculate payment dates
            $paymentDaysAdvance = $business->payment_days_advance ?? 7;
            $paymentDaysFinal = $business->payment_days_final ?? 1;
            $advancePercentage = is_numeric($business->advance_percentage) ? $business->advance_percentage : 10;

            $advanceAmount = round(($totalAmount * $advancePercentage) / 100, 2);
            $today = Carbon::now();
            $advanceDue = $today->copy()->addDays($paymentDaysAdvance);
            $finalDue = $localMoment->copy()->subDays($paymentDaysFinal);
            $finalAmount = round($totalAmount - $advanceAmount, 2);

            if ($advanceDue->isAfter($finalDue)) {
                $advanceDue = $finalDue->copy();
            }

            // Create booking
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
                'guests' => $data['guests'] ?? null,
                'start_time' => $formattedTime['start_time'],
                'end_time' => $formattedTime['end_time'],
                'extra_services' => $selectedExtras,
                'advance_percentage' => $advancePercentage,
                'advance_amount' => $advanceAmount,
                'final_amount' => $finalAmount,
                'advance_due_date' => $advanceDue->toDateString(),
                'final_due_date' => $finalDue->toDateString(),
                'status' => 'pending',
            ]);

            // Update timings
            $timingsVenue = $timings->timings_venue;
            $timingsVenue[$dayOfWeek][$data['time_slot']]['status'] = 'booked';
            $timings->timings_venue = $timingsVenue;
            $timings->save();

            // Check if all slots are booked
            $bookedSlots = Booking::where('business_id', $business->id)
                ->whereDate('event_date', $localMoment->toDateString())
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->pluck('time_slot')
                ->toArray();

            $allSlots = array_keys($dayTimings);
            $allBooked = empty(array_diff($allSlots, $bookedSlots));

            if ($allBooked && !in_array($localDate, $timings->unavailable_dates)) {
                $unavailableDates = $timings->unavailable_dates ?? [];
                $unavailableDates[] = $localDate;
                $timings->unavailable_dates = $unavailableDates;
                $timings->save();
            }

            // Send emails
            $this->emailService->sendHostBookingEmail($host, $business, $formattedTime['formatted']);
            $this->emailService->sendVendorBookingEmail($vendor, $business, $formattedTime['formatted'], $host->full_name);

            // Auto-accept booking
            try {
                $baseUrl = config('app.api_url', 'https://api.wedbooki.com');
                Http::put("{$baseUrl}/api/v1/vendor/accept-booking/{$hostId}", [
                    'bookingId' => $booking->id,
                ]);
            } catch (Exception $e) {
                \Log::warning('Auto-accept booking failed: ' . $e->getMessage());
            }

            return [
                'message' => 'Venue booked successfully.',
                'booking' => $booking->load(['business', 'package', 'vendor']),
                'bookingId' => $booking->custom_booking_id,
                'priceBreakdown' => $priceBreakdown,
                'slotStatus' => $timings->timings_venue[$dayOfWeek][$data['time_slot']]['status'],
            ];
        });
    }

    public function formatBookingTime($booking)
    {
        // Format booking time for display
        return $booking;
    }

    public function cancelBooking($hostId, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('host_id', $hostId)
            ->first();

        if (!$booking) {
            throw new Exception('Booking not found or unauthorized.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return [
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking,
        ];
    }

    public function getVendorAvailableSlots($vendorId, $date, $timezone)
    {
        // Implementation for getting available slots
        return [];
    }

    public function getSlotsForDate($vendorId, $date, $timezone)
    {
        // Implementation for getting slots for date
        return [];
    }
}
