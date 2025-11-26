<?php

namespace App\Src\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class CounterService
{
    /**
     * Get the next counter value and optionally prepend a prefix.
     *
     * @param string $name Name of the counter (maps to `name` column in table)
     * @param string|null $prefix Optional prefix for the generated ID
     * @return string
     * @throws Exception
     */
    public function getNextCounter(string $name, string $prefix = null): string
    {
        return DB::transaction(function () use ($name, $prefix) {
            // Lock the row for update
            $counter = DB::table('counters')
                ->where('name', $name)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                // If counter row doesn't exist, create it starting at 1
                DB::table('counters')->insert([
                    'name' => $name,
                    'seq' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $seq = 1;
            } else {
                // Increment the sequence
                $seq = $counter->seq + 1;

                DB::table('counters')
                    ->where('id', $counter->id)
                    ->update([
                        'seq' => $seq,
                        'updated_at' => now(),
                    ]);
            }

            // Return ID with optional prefix
            return $prefix ? $prefix . $seq : (string) $seq;
        });
    }
}
