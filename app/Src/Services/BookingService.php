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


    public function createVendorBooking($host, array $data)
    {
        // Fetch business
        $business = Business::find($data['business_id']);
        if (!$business) {
            throw new \Exception('Business not found.');
        }

        // Fetch package if exists
        $selectedPackage = null;
        if (!empty($data['package_id'])) {
            $selectedPackage = Package::find($data['package_id']);
            if (!$selectedPackage) {
                throw new \Exception('Package not found.');
            }
        }

        // Vendor related to business
        $vendor = Vendor::where('business_id', $business->id)
            ->select('id', 'email', 'full_name')
            ->first();

        if (!$vendor) {
            throw new \Exception('Vendor not found for this business.');
        }

        // Timings
        $timings = VendorTiming::where('business_id', $business->id)->first();
        if (!$timings) {
            throw new \Exception('No timing information found for this vendor.');
        }

        // Parse event datetime (support 24-hour format)
        try {
            $localStart = Carbon::createFromFormat(
                'd-m-Y H:i',
                $data['event_date'].' '.$data['start_time'],
                $data['timezone']
            );

            $localEnd = Carbon::createFromFormat(
                'd-m-Y H:i',
                $data['event_date'].' '.$data['end_time'],
                $data['timezone']
            );
        } catch (\Exception $e) {
            throw new \Exception('Invalid date/time format. Use d-m-Y and H:i format.');
        }

        if ($localEnd->lte($localStart)) {
            throw new \Exception('End time must be after start time.');
        }

        $utcStart = $localStart->copy()->setTimezone('UTC');
        $utcEnd = $localEnd->copy()->setTimezone('UTC');
        $localDateOnly = $localStart->toDateString();

        // Check unavailable dates
        $unavailableDates = $timings->unavailable_dates ?? [];
        if (is_string($unavailableDates)) {
            $unavailableDates = json_decode($unavailableDates, true) ?? [];
        }

        if (in_array($localDateOnly, $unavailableDates)) {
            throw new \Exception('The vendor is unavailable on the selected date.');
        }

        // Flatten available slots
        $weekday = strtolower($localStart->format('l'));
        $rawForDay = json_decode($timings->timings_service_weekly ?? '[]', true)[$weekday] ?? [];

        $flatSlotsMeta = [];
        foreach ($rawForDay as $idx => $slot) {
            $flatSlotsMeta[] = ['slot'=>$slot, 'meta'=>['source'=>'weekly','index'=>$idx]];
        }

        if (empty($flatSlotsMeta)) {
            throw new \Exception("No available slots on {$weekday}.");
        }

        $requestedSlot = [
            'start' => $localStart->format('H:i'),
            'end'   => $localEnd->format('H:i')
        ];

        $matched = collect($flatSlotsMeta)->first(function ($item) use ($requestedSlot) {
            return $item['slot'] && $item['slot']['status'] === 'active'
                && $item['slot']['start'] === $requestedSlot['start']
                && $item['slot']['end'] === $requestedSlot['end'];
        });

        if (!$matched) {
            throw new \Exception("Selected slot {$requestedSlot['start']}–{$requestedSlot['end']} is not available.");
        }

        // Determine time_slot
        $hour = Carbon::createFromFormat('H:i', $matched['slot']['start'])->hour;
        if ($hour >= 5 && $hour < 12) $bookingTimeSlot = 'morning';
        elseif ($hour >= 12 && $hour < 17) $bookingTimeSlot = 'afternoon';
        else $bookingTimeSlot = 'evening';

        // Check overlapping bookings
        $overlapping = Booking::where('business_id', $business->id)
            ->whereDate('event_date', $utcStart->toDateString())
            ->where(function ($q) use ($utcStart, $utcEnd) {
                $q->where('start_time','<',$utcEnd)
                    ->where('end_time','>',$utcStart);
            })
            ->whereNotIn('status', ['rejected','cancelled'])
            ->first();

        if ($overlapping) {
            throw new \Exception('Vendor already booked for this time range.');
        }

        // Extra services
        $selectedExtras = [];
        if (!empty($data['extra_services']) && is_array($data['extra_services'])) {
            foreach ($data['extra_services'] as $id) {
                $service = ExtraService::where('business_id', $business->id)
                    ->where('id', $id)
                    ->first();
                if (!$service) throw new \Exception('Invalid extra service selected');
                $selectedExtras[] = ['name'=>$service->name,'price'=>$service->price];
            }
        }

        // Price calculation
        $packagePrice = $selectedPackage->price ?? 0;
        $discountedPrice = $selectedPackage->discount ?? 0;
        $extrasTotal = collect($selectedExtras)->sum('price');
        $base = $discountedPrice > 0 ? $discountedPrice : $packagePrice;
        $totalAmount = $base + $extrasTotal;
        $priceBreakdown = [
            'basePrice' => $packagePrice,
            'extras' => $extrasTotal,
            'discountedPrice' => $discountedPrice,
            'finalPrice' => $totalAmount
        ];

        // Custom booking ID using counters table
        $counter = Counter::firstOrCreate(['name'=>'vendor_booking_id'], ['seq'=>400]);
        $customBookingId = 'WB-B'.$counter->seq;
        $counter->increment('seq');

        // Payment calculations
        $advancePercentage = $business->advance_percentage ?? 10;
        $advanceAmount = round(($totalAmount * $advancePercentage)/100,2);
        $today = Carbon::now();
        $advanceDue = $today->copy()->addDays($business->payment_days_advance ?? 7);
        $finalDue = $localStart->copy()->subDays($business->payment_days_final ?? 1);
        $finalAmount = round($totalAmount - $advanceAmount, 2);
        if ($advanceDue->gt($finalDue)) $advanceDue = $finalDue->copy();

        // Save booking
        $booking = Booking::create([
            'host_id'           => $host->id,
            'business_id'       => $business->id,
            'venue_id'          => $vendor->id,
            'package_id'        => $selectedPackage->id ?? null,
            'event_date'        => $utcStart->toDateString(),
            'start_time'        => $utcStart,
            'end_time'          => $utcEnd,
            'time_slot'         => $bookingTimeSlot,
            'timezone'          => $data['timezone'],
            'amount'            => $totalAmount,
            'custom_booking_id' => $customBookingId,
            'extra_services'    => $selectedExtras,
            'advance_percentage'=> $advancePercentage,
            'advance_amount'    => $advanceAmount,
            'final_amount'      => $finalAmount,
            'advance_due_date'  => $advanceDue->toDateString(),
            'final_due_date'    => $finalDue->toDateString(),
        ]);

        return [
            'booking' => $booking,
            'bookingId' => $customBookingId,
            'priceBreakdown' => $priceBreakdown,
        ];
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
