<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_in_maintenance_mode')->default(false);

            $table->longText('privacy_policy')->nullable();
            $table->timestamp('privacy_policy_updated_at')->nullable();

            $table->longText('terms_of_service')->nullable();
            $table->timestamp('terms_of_service_updated_at')->nullable();

            $table->longText('refund_policy')->nullable();
            $table->timestamp('refund_policy_updated_at')->nullable();

            // General site info
            $table->string('site_name')->default('My App');
            $table->string('site_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('site_email')->nullable();
            $table->string('site_phone')->nullable();

            // Social links
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();

            // SEO settings
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Maintenance & operational settings
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();

            // Other useful global settings
            $table->string('default_timezone')->default('UTC');
            $table->string('default_language')->default('en');
            $table->string('currency')->default('USD');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_settings');
    }
};
