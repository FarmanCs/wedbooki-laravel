<?php

namespace App\Src\Services\Vendor;

use App\Models\Counter;

class CounterService
{
    /**
     * Get next counter value for a given type
     *
     * @param string $type
     * @param string $prefix
     * @return string
     */
    public function getNextCounter(string $type, string $prefix = ''): string
    {
        $counter = Counter::where('type', $type)->lockForUpdate()->first();

        if (!$counter) {
            $counter = Counter::create([
                'type' => $type,
                'value' => 1
            ]);
            return $prefix . '1';
        }

        $counter->increment('value');

        return $prefix . $counter->value;
    }
}
