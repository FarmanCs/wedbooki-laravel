<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Vendor\Vendor;
use Database\Seeders\Admin\AdminDatabaseSeeder;
use Database\Seeders\Admin\CategorySeeder;
use Database\Seeders\Host\HostSeeder;
use Database\Seeders\Vendor\VendorDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{


    public function run(): void
    {
        $this->call([
            HostSeeder::class,
            AdminDatabaseSeeder::class,
            VendorDatabaseSeeder::class,
        ]);
    }


}
