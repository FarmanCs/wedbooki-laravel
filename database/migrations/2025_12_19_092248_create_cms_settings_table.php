<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_settings');
    }
};
