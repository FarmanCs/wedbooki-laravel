<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->bigInteger('phone_no')->nullable();
            $table->string('pending_email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->json('languages')->nullable();
            $table->integer('team_members')->nullable();
            $table->json('specialties')->nullable();
            $table->text('about')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('role')->default('vendor');
            $table->string('password');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('postal_code')->nullable();
            $table->integer('otp')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('two_factor_code')->nullable();
            $table->timestamp('two_factor_code_expires')->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->enum('profile_verification', ['pending', 'verified', 'approved', 'under_review', 'rejected', 'banned'])->default('approved');
            $table->boolean('email_verified')->default(false);
            $table->string('stripe_account_id')->nullable();
            $table->string('bank_last4')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('payout_currency')->default('usd');
            $table->string('custom_vendor_id')->nullable();
            $table->string('google_id')->nullable();
            $table->enum('signup_method', ['email', 'google', 'apple'])->default('email');
            $table->string('cover_image')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('account_deactivated')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('account_soft_deleted')->default(false);
            $table->timestamp('account_soft_deleted_at')->nullable();
            $table->integer('auto_hard_delete_after_days')->default(30);
            $table->softDeletes();
            $table->timestamps();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
