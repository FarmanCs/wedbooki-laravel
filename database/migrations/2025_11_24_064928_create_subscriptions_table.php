<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->string('name')->default('Basic');

            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');

            $table->date('start_date');
            $table->date('end_date');
            $table->enum('subscription_type', ['monthly', 'quarterly', 'yearly']);
            $table->integer('credits')->default(0);
            $table->timestamp('last_credit_date')->useCurrent();
            $table->timestamp('last_reminder_sent')->nullable();
            $table->timestamp('last_renewal_attempt')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
