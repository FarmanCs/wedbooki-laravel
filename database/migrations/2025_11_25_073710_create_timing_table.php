<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timings', function (Blueprint $table) {
            $table->id();

            // Reference to business
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');

            // Venue timings (morning, afternoon, evening)
            $table->json('timings_venue')->nullable();

            // Slot duration in minutes
            $table->integer('slot_duration')->nullable();

            // Working hours (map of day => {start, end})
            $table->json('working_hours')->nullable();

            // Weekly service timings (map of day => array of slots)
            $table->json('timings_service_weekly')->nullable();

            // Unavailable dates
            $table->json('unavailable_dates')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timings');
    }
};
