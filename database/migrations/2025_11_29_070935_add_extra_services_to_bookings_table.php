<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add extra_services JSON column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'extra_services')) {
                $table->json('extra_services')->nullable()->after('custom_booking_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'extra_services')) {
                $table->dropColumn('extra_services');
            }
        });
    }
};
