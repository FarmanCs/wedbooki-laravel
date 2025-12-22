<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_social_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('platform');
            $table->string('device_id');
            $table->timestamp('clicked_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['business_id', 'device_id', 'platform']);
            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_social_clicks');
    }
};
