<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('cascade');
            $table->json('timings_venue')->nullable(); // stores complex venue timing structure
            $table->integer('slot_duration')->nullable();
            $table->json('working_hours')->nullable(); // stores Map structure
            $table->json('timings_service_weekly')->nullable(); // stores weekly service timings
            $table->json('unavailable_dates')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_timings');
    }
};
