<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('country')->default('United Kingdom');
            $table->string('city')->default('');
            $table->string('zip')->nullable();
            $table->string('street_address')->default('');
            $table->string('company_name')->nullable();
            $table->text('business_desc')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            $table->string('venue_type')->nullable();
            $table->enum('member_type', ['general', 'premium'])->default('general');
            $table->string('business_registration')->nullable();
            $table->string('business_license_number')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('business_type', ['partnership', 'llc', 'corporation'])->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->json('features')->nullable();
            $table->enum('profile_verification', ['under_review', 'rejected', 'verified'])->default('verified');
            $table->json('services')->nullable();
            $table->json('faqs')->nullable();
            $table->json('portfolio_images')->nullable();
            $table->json('videos')->nullable();
            $table->string('street')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('social_count')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->integer('payment_days_advance')->default(7);
            $table->integer('payment_days_final')->default(1);
            $table->integer('services_radius')->default(50);
            $table->integer('advance_percentage')->default(10);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
