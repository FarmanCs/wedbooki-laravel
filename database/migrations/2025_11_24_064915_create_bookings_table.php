<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('venue_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('custom_booking_id')->unique();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('advance_percentage', 5, 2)->nullable();
            $table->decimal('advance_amount', 15, 2)->nullable();
            $table->decimal('final_amount', 15, 2)->nullable();
            $table->date('advance_due_date')->nullable();
            $table->date('final_due_date')->nullable();
            $table->boolean('advance_paid')->default(false);
            $table->boolean('final_paid')->default(false);
            $table->json('extra_services')->nullable();
            $table->date('event_date')->nullable();
            $table->string('timezone')->nullable();
            $table->enum('time_slot', ['morning', 'afternoon', 'evening'])->nullable();
            $table->integer('guests')->default(0);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled', 'confirmed', 'completed'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('payment_status', ['unpaid', 'advancePaid', 'refunded', 'fullyPaid'])->default('unpaid');
            $table->boolean('is_synced_with_calendar')->default(false);
            $table->timestamps();

            $table->index('host_id');
            $table->index('business_id');
            $table->index('custom_booking_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
