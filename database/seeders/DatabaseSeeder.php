<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Vendor\Vendor;
use Database\Seeders\Admin\AdminDatabaseSeeder;
use Database\Seeders\Host\HostSeeder;
use Database\Seeders\Vendor\CategorySeeder;
use Database\Seeders\Vendor\VendorDatabaseSeeder;
use Database\Seeders\Vendor\VendorSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{


    public function run(): void
    {
        $this->call([
            CategorySeeder::Class,
            HostSeeder::class,
            AdminDatabaseSeeder::class,
            VendorDatabaseSeeder::class,


        ]);
    }


}
