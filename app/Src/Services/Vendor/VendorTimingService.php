<?php

namespace App\Src\Services\Vendor;

use App\Models\Vendor\Business;
use App\Models\Vendor\Category;
use App\Models\Vendor\Timing;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorTimingService
{
    public function updateVendorTimings($businessId, array $data): JsonResponse
    {
        // Validate business exists
        $business = Business::with('category')->find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $category = Category::find($business->category_id);
        $updateData = ['business_id' => $businessId];

        // Handle venue timings
        if ($category->type === 'venue' && isset($data['timings_venue'])) {
            $updateData['timings_venue'] = $this->cleanTimingsVenue($data['timings_venue']);
        }
        // Handle service timings
        elseif ($category->type !== 'venue' && isset($data['working_hours']) && isset($data['slot_duration'])) {
            $updateData['working_hours'] = $data['working_hours'];
            $updateData['slot_duration'] = $data['slot_duration'];
            $updateData['timings_service_weekly'] = $this->generateWeeklyServiceSlots(
                $data['working_hours'],
                $data['slot_duration']
            );
        } else {
            return response()->json(['message' => 'Invalid timing data'], 400);
        }

        // Handle unavailable dates
        if (isset($data['unavailable_dates']) && is_array($data['unavailable_dates'])) {
            $updateData['unavailable_dates'] = $data['unavailable_dates'];
        }

        // Update or create timing
        $timing = Timing::updateOrCreate(
            ['business_id' => $businessId],
            $updateData
        );

        return response()->json([
            'message' => 'Vendor timings updated successfully',
            'data' => $timing
        ], 200);
    }

    public function getServiceVendorTimings($businessId): JsonResponse
    {
        $timing = Timing::where('business_id', $businessId)
            ->select('working_hours', 'slot_duration', 'timings_service_weekly')
            ->first();

        if (!$timing || !$timing->working_hours || !$timing->timings_service_weekly) {
            return response()->json(['message' => 'Service vendor timings not found'], 404);
        }

        return response()->json([
            'message' => 'Service vendor timings fetched successfully',
            'data' => [
                'slot_duration' => $timing->slot_duration,
                'working_hours' => $timing->working_hours,
                'timings_service_weekly' => $timing->timings_service_weekly,
            ]
        ], 200);
    }

    public function getVenueVendorTimings($businessId): JsonResponse
    {
        $timing = Timing::where('business_id', $businessId)
            ->select('timings_venue')
            ->first();

        if (!$timing || !$timing->timings_venue) {
            return response()->json(['message' => 'Venue timings not found'], 404);
        }

        return response()->json([
            'message' => 'Venue timings fetched successfully',
            'data' => $timing->timings_venue
        ], 200);
    }

    public function addUnavailableDate($businessId, array $data): JsonResponse
    {
        $validator = Validator::make($data, ['date' => 'required|date']);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $timing = Timing::where('business_id', $businessId)->first();

        if (!$timing) {
            return response()->json(['message' => 'Vendor timings not found'], 404);
        }

        $unavailableDates = $timing->unavailable_dates ?? [];

        if (in_array($data['date'], $unavailableDates)) {
            return response()->json(['message' => 'Date already marked as unavailable'], 400);
        }

        $unavailableDates[] = $data['date'];
        $timing->unavailable_dates = $unavailableDates;
        $timing->save();

        return response()->json([
            'message' => 'Unavailable date added successfully',
            'data' => $timing->unavailable_dates
        ], 200);
    }

    public function makeDateAvailable($businessId, array $data): JsonResponse
    {
        $validator = Validator::make($data, ['date' => 'required|date']);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $timing = Timing::where('business_id', $businessId)->first();

        if (!$timing) {
            return response()->json(['message' => 'Vendor timings not found'], 404);
        }

        $unavailableDates = $timing->unavailable_dates ?? [];
        $key = array_search($data['date'], $unavailableDates);

        if ($key === false) {
            return response()->json(['message' => 'Date not found in unavailable list'], 404);
        }

        unset($unavailableDates[$key]);
        $timing->unavailable_dates = array_values($unavailableDates);
        $timing->save();

        return response()->json([
            'message' => 'Date made available successfully',
            'data' => $timing->unavailable_dates
        ], 200);
    }

    public function getUnavailableDates($businessId): JsonResponse
    {
        $timing = Timing::where('business_id', $businessId)->first();

        if (!$timing) {
            return response()->json(['message' => 'Vendor timings not found'], 404);
        }

        return response()->json([
            'message' => 'Unavailable dates fetched successfully',
            'data' => $timing->unavailable_dates ?? []
        ], 200);
    }

    public function deleteUnavailableDate(array $data): JsonResponse
    {
        // Implementation for deleting specific unavailable date
        // Similar to makeDateAvailable
    }

    public function updateUnavailableDates(array $data): JsonResponse
    {
        // Implementation for bulk updating unavailable dates
    }

    public function getSlotsForDate($vendorId): JsonResponse
    {
        // Implementation for getting available slots for a specific date
    }

    public function getVendorAvailableSlots($vendorId): JsonResponse
    {
        // Implementation for getting all available slots
    }

    private function cleanTimingsVenue(array $timings): array
    {
        // Clean and format venue timings
        // Remove empty values, standardize format
        return $timings;
    }

    private function generateWeeklyServiceSlots(array $workingHours, int $slotDuration): array
    {
        // Generate time slots based on working hours and duration
        $slots = [];

        foreach ($workingHours as $day => $hours) {
            $daySlots = [];
            $start = Carbon::parse($hours['start']);
            $end = Carbon::parse($hours['end']);

            while ($start->lt($end)) {
                $slotEnd = $start->copy()->addMinutes($slotDuration);
                if ($slotEnd->lte($end)) {
                    $daySlots[] = [
                        'start' => $start->format('h:i A'),
                        'end' => $slotEnd->format('h:i A'),
                        'status' => 'active'
                    ];
                }
                $start = $slotEnd;
            }

            $slots[$day] = $daySlots;
        }

        return $slots;
    }
}
