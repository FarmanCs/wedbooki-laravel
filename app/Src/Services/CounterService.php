<?php

namespace App\Src\Services;

use App\Models\Counter;
use Illuminate\Support\Facades\DB;

class CounterService
{
    /**
     * Get the next counter value for a given type
     *
     * @param string $type Counter type (e.g., 'venue_booking_id')
     * @param string $prefix Prefix for the counter (e.g., 'WB-B400')
     * @return string The formatted counter value
     */
    public function getNextCounter($type, $prefix)
    {
        return DB::transaction(function () use ($type, $prefix) {
            // Find or create counter
            $counter = Counter::where('type', $type)->lockForUpdate()->first();

            if (!$counter) {
                $counter = Counter::create([
                    'type' => $type,
                    'value' => 1,
                    'prefix' => $prefix,
                ]);

                return $prefix . '1';
            }

            // Increment counter
            $counter->increment('value');

            // Return formatted counter
            return $prefix . $counter->value;
        });
    }

    /**
     * Reset counter to a specific value
     */
    public function resetCounter($type, $value = 0)
    {
        $counter = Counter::where('type', $type)->first();

        if ($counter) {
            $counter->value = $value;
            $counter->save();
        }

        return $counter;
    }

    /**
     * Get current counter value without incrementing
     */
    public function getCurrentCounter($type)
    {
        $counter = Counter::where('type', $type)->first();

        if (!$counter) {
            return 0;
        }

        return $counter->value;
    }
}
