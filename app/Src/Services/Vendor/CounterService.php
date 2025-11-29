<?php

namespace App\Src\Services\Vendor;

use App\Models\Counter;

class CounterService
{
    public function getNextCounter(string $name, string $prefix = ''): string
    {
        $counter = Counter::where('name', $name)->lockForUpdate()->first();

        if (!$counter) {
            $counter = Counter::create([
                'name' => $name,
                'seq' => 1
            ]);

            return $prefix . '1';
        }

        $counter->increment('seq');

        return $prefix . $counter->seq;
    }
}
