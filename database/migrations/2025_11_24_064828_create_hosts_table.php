<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('partner_full_name')->nullable();
            $table->string('partner_email')->nullable();
            $table->string('country')->default('United Kingdom');
            $table->string('email')->unique()->nullable();
            $table->string('linked_email')->nullable();
            $table->string('country_code')->default('+44');
            $table->bigInteger('phone_no')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('about')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->unique()->nullable();
            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->timestamp('google_token_expiry')->nullable();
            $table->boolean('google_calendar_connected')->default(false);
            $table->string('google_email')->nullable();
            $table->string('google_name')->nullable();
            $table->string('apple_id')->unique()->nullable();
            $table->enum('signup_method', ['email', 'google', 'apple'])->default('email');
            $table->enum('status', ['approved', 'pending', 'rejected', 'blocked', 'Banned',])->default('Pending');
            $table->string('role')->default('host');
            $table->integer('otp')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('invite_image_url')->nullable();
            $table->string('pending_email')->nullable();
            $table->string('category')->nullable();
            $table->string('event_type')->nullable();
            $table->integer('estimated_guests')->nullable();
            $table->decimal('event_budget', 15, 2)->nullable();
            $table->timestamp('join_date')->useCurrent();
            $table->boolean('account_deactivated')->default(false);
            $table->boolean('account_soft_deleted')->default(false);
            $table->timestamp('account_soft_deleted_at')->nullable();
            $table->integer('auto_hard_delete_after_days')->default(30);
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('google_id');
            $table->index('apple_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
