<?php

namespace App\Src\Services;

use App\Models\Counter;
use App\Models\Host\Host;
use App\Models\Services\ExtraService;
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


    public function createBooking(array $data)
    {
        $eventDate = Carbon::parse($data['event_date']);
        $host = Host::find($data['host_id']);
        $business = Business::with(['extraServices', 'vendor', 'packages', 'timings' => function($qry) use($data, $eventDate){
            $day = strtolower($eventDate->format('l'));
            $qry->where('working_hours', );
        }])->find($data['business_id']);
dd($business->toArray());

        // Convert event date/time to Carbon using 12-hour format
        $localStart = Carbon::createFromFormat('d-m-Y h:i A', $data['event_date'] . ' ' . $data['start_time'], $data['timezone']);
        $localEnd = Carbon::createFromFormat('d-m-Y h:i A', $data['event_date'] . ' ' . $data['end_time'], $data['timezone']);


        $utcStart = $localStart->copy()->setTimezone('UTC');
        $utcEnd = $localEnd->copy()->setTimezone('UTC');
        $localDateOnly = $localStart->format('Y-m-d');

        // Check unavailable dates
        $unavailableDates = $timing->unavailable_dates ? $timing->unavailable_dates : [];
        if (in_array($localDateOnly, $unavailableDates)) {
            abort(400, 'The vendor is unavailable on the selected date.');
        }

        // Determine day of week and get available slots
        $weekday = strtolower($localStart->format('l'));
        $rawForDay = null;

        if ($timing->timings_service_weekly) {
            $weekly = $timing->timings_service_weekly;
            $rawForDay = $weekly[$weekday] ?? null;
        }

        if (!$rawForDay && $timing->timings_venue) {
            $venue = $timing->timings_venue;
            $rawForDay = $venue[$weekday] ?? null;
        }

        $flatSlotsMeta = [];
        if (is_array($rawForDay)) {
            foreach ($rawForDay as $idx => $slot) {
                $flatSlotsMeta[] = ['slot' => $slot, 'meta' => ['source' => 'weekly', 'index' => $idx]];
            }
        } elseif (is_array($rawForDay)) {
            foreach ($rawForDay as $period => $val) {
                if (!$val) continue;
                if (is_array($val)) {
                    foreach ($val as $idx => $slot) {
                        $flatSlotsMeta[] = ['slot' => $slot, 'meta' => ['source' => 'venue', 'period' => $period, 'index' => $idx]];
                    }
                } else {
                    $flatSlotsMeta[] = ['slot' => $val, 'meta' => ['source' => 'venue', 'period' => $period]];
                }
            }
        }

        if (empty($flatSlotsMeta)) {
            abort(400, "No available slots on $weekday.");
        }

        $requestedSlot = [
            'start' => $localStart->format('h:i A'),
            'end' => $localEnd->format('h:i A')
        ];

        $matched = collect($flatSlotsMeta)->first(function ($item) use ($requestedSlot) {
            return $item['slot']['status'] === 'active' &&
                $item['slot']['start'] === $requestedSlot['start'] &&
                $item['slot']['end'] === $requestedSlot['end'];
        });

        if (!$matched) {
            abort(400, "Selected slot {$requestedSlot['start']}–{$requestedSlot['end']} is not available.");
        }

        // Determine time_slot
        $allowedSlots = ['morning', 'afternoon', 'evening'];
        $timeSlot = null;
        if (isset($matched['meta']['period']) && in_array($matched['meta']['period'], $allowedSlots)) {
            $timeSlot = $matched['meta']['period'];
        } else {
            $hour = (int)$localStart->format('H');
            $timeSlot = $hour >= 5 && $hour < 12 ? 'morning' : ($hour >= 12 && $hour < 17 ? 'afternoon' : 'evening');
        }

        // Check overlapping bookings
        $overlap = Booking::where('business_id', $business->id)
            ->where('event_date', $utcStart->format('Y-m-d'))
            ->where(function ($q) use ($utcStart, $utcEnd) {
                $q->whereBetween('start_time', [$utcStart, $utcEnd])
                    ->orWhereBetween('end_time', [$utcStart, $utcEnd]);
            })
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->first();

        if ($overlap) {
            abort(409, 'Vendor already booked for this time range.');
        }

        // Calculate extras and pricing
        $selectedExtras = [];
        if (!empty($data['extra_services'])) {
            foreach ($data['extra_services'] as $id) {
                $service = collect($business->extraServices)->firstWhere('id', $id);
                if (!$service) abort(400, 'Invalid extra service selected.');
                $selectedExtras[] = ['name' => $service->name, 'price' => $service->price];
            }
        }

        $packagePrice = $package->price ?? 0;
        $discountedPrice = $package->discount ?? 0;
        $extrasTotal = collect($selectedExtras)->sum('price');
        $basePrice = $discountedPrice > 0 ? $discountedPrice : $packagePrice;
        $totalAmount = $basePrice + $extrasTotal;

        // Custom booking ID
        $customBookingId = $this->getNextCounter('vendor_booking_id', 'WB-B400');

        // Payment calculations
        $advancePercentage = $business->advance_percentage ?? 10;
        $advanceAmount = round(($totalAmount * $advancePercentage) / 100, 2);
        $finalAmount = round($totalAmount - $advanceAmount, 2);

        $paymentDaysAdvance = $business->payment_days_advance ?? 7;
        $paymentDaysFinal = $business->payment_days_final ?? 1;

        $today = Carbon::now();
        $advanceDue = $today->copy()->addDays($paymentDaysAdvance);
        $finalDue = $localStart->copy()->subDays($paymentDaysFinal);
        if ($advanceDue->gt($finalDue)) $advanceDue = $finalDue->copy();

        // Create booking
        $booking = Booking::create([
            'host_id' => $host->id,
            'business_id' => $business->id,
            'venue_id' => $vendor->id,
            'package_id' => $package->id ?? null,
            'event_date' => $utcStart->format('Y-m-d'),
            'start_time' => $utcStart,
            'end_time' => $utcEnd,
            'time_slot' => $timeSlot,
            'timezone' => $data['timezone'],
            'amount' => $totalAmount,
            'custom_booking_id' => $customBookingId,
            'extra_services' => json_encode($selectedExtras),
            'advance_percentage' => $advancePercentage,
            'advance_amount' => $advanceAmount,
            'final_amount' => $finalAmount,
            'advance_due_date' => $advanceDue,
            'final_due_date' => $finalDue,
        ]);

        return $booking;
    }


    private function getNextCounter($counterName, $prefix)
    {
        $counter = DB::table('counters')->where('name', $counterName)->lockForUpdate()->first();
        if (!$counter) {
            DB::table('counters')->insert([
                'name' => $counterName,
                'seq' => 3000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $counter = DB::table('counters')->where('name', $counterName)->first();
        }
        DB::table('counters')->where('name', $counterName)->update(['seq' => $counter->seq + 1, 'updated_at' => now()]);
        return $prefix . ($counter->seq + 1);
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
