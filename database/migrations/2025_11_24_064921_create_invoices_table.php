<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('set null');
            $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');

            $table->string('sender_name')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('invoice_number')->unique()->nullable();
            $table->enum('payment_type', ['advance', 'final', 'full'])->nullable();

            $table->decimal('total_amount', 10, 2);
            $table->decimal('advance_amount', 10, 2)->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable();
            $table->decimal('base_amount_paid', 10, 2)->nullable();
            $table->decimal('platform_fee_from_user', 10, 2)->nullable();
            $table->decimal('total_user_paid', 10, 2)->nullable();
            $table->decimal('vendor_share', 10, 2)->nullable();
            $table->decimal('platform_share', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('vendor_plan_name')->nullable();

            $table->timestamp('advance_paid_date')->nullable();
            $table->timestamp('final_paid_date')->nullable();

            $table->boolean('is_advance_paid')->default(false);
            $table->boolean('is_final_paid')->default(false);

            $table->timestamp('advance_due_date')->nullable();
            $table->timestamp('final_due_date')->nullable();
            $table->integer('advance_percentage')->default(10);
            $table->boolean('full_payment_only')->default(false);

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
