<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['initiated', 'successful', 'failed', 'refunded'])->default('initiated');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('sender_type')->nullable();
            $table->string('receiver_type')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('acquirer_ref')->nullable();
            $table->string('profile_id')->nullable();
            $table->string('tran_type')->nullable();
            $table->string('tran_class')->nullable();
            $table->string('cart_id')->nullable();
            $table->string('cart_currency')->nullable();
            $table->text('comments')->nullable();
            $table->json('request_body')->nullable();
            $table->json('click_pay_response')->nullable();
            $table->json('click_pay_callback')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
