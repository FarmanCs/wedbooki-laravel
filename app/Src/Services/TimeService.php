<?php

namespace App\Src\Services;

use Carbon\Carbon;
use Exception;

class TimeService
{
    /**
     * Convert local date and time to UTC
     */
    public function getUTCFromLocal($eventDate, $timeSlot, $timezone)
    {
        try {
            // Parse the date in format DD-MM-YYYY
            $localDate = Carbon::createFromFormat('d-m-Y', $eventDate, $timezone);

            // Parse time slot (e.g., "morning", "afternoon", "evening")
            // You'll need to define these mappings based on your slot definitions
            $timeMap = [
                'morning' => '09:00',
                'afternoon' => '14:00',
                'evening' => '18:00',
                'night' => '20:00',
            ];

            $time = $timeMap[$timeSlot] ?? '12:00';
            list($hour, $minute) = explode(':', $time);

            $localDateTime = $localDate->setTime($hour, $minute);

            // Convert to UTC
            $utcDateTime = $localDateTime->copy()->setTimezone('UTC');

            return $utcDateTime;
        } catch (Exception $e) {
            throw new Exception('Invalid date, time slot, or timezone provided.');
        }
    }

    /**
     * Format slot timing for display
     */
    public function formatSlotTime($slotTiming, $localDate, $timeSlot, $timezone)
    {
        try {
            $startTime = $slotTiming['start_time'] ?? '09:00';
            $endTime = $slotTiming['end_time'] ?? '18:00';

            // Parse the local date
            $date = Carbon::createFromFormat('d-m-Y', $localDate, $timezone);

            // Create start and end datetime
            list($startHour, $startMinute) = explode(':', $startTime);
            list($endHour, $endMinute) = explode(':', $endTime);

            $startDateTime = $date->copy()->setTime($startHour, $startMinute);
            $endDateTime = $date->copy()->setTime($endHour, $endMinute);

            // Format for display
            $formatted = sprintf(
                '%s from %s to %s',
                $date->format('l, F j, Y'),
                $startDateTime->format('g:i A'),
                $endDateTime->format('g:i A')
            );

            return [
                'formatted' => $formatted,
                'start_time' => $startDateTime->toDateTimeString(),
                'end_time' => $endDateTime->toDateTimeString(),
            ];
        } catch (Exception $e) {
            throw new Exception('Error formatting slot time: ' . $e->getMessage());
        }
    }

    /**
     * Get day of week from date
     */
    public function getDayOfWeek($date, $timezone)
    {
        $carbon = Carbon::parse($date, $timezone);
        return strtolower($carbon->format('l'));
    }

    /**
     * Check if date is in the past
     */
    public function isPastDate($date, $timezone)
    {
        $carbon = Carbon::parse($date, $timezone);
        $now = Carbon::now($timezone);

        return $carbon->lt($now);
    }
}
