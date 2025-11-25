<?php

// ======================
// INVOICES
// ======================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('set null');
            $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('invoice_number')->unique()->nullable();
            $table->enum('payment_type', ['advance', 'final'])->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('advance_amount', 15, 2)->nullable();
            $table->decimal('remaining_amount', 15, 2)->nullable();
            $table->date('advance_paid_date')->nullable();
            $table->date('final_paid_date')->nullable();
            $table->boolean('is_advance_paid')->default(false);
            $table->boolean('is_final_paid')->default(false);
            $table->date('advance_due_date')->nullable();
            $table->date('final_due_date')->nullable();
            $table->decimal('advance_percentage', 5, 2)->default(30);
            $table->timestamps();

            $table->index('booking_id');
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
