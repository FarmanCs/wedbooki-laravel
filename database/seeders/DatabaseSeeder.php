<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Vendor\Vendor;
use Database\Seeders\Host\HostSeeder;
use Database\Seeders\Vendor\CategorySeeder;
use Database\Seeders\Vendor\VendorSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{


    public function run(): void
    {
        $this->call([
            SupportQuerySeeder::class,
            CategorySeeder::Class,
            VendorSeeder::class,
            HostSeeder::class,

        ]);
    }


}
