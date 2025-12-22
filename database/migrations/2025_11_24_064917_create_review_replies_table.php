<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->nullable()->constrained('reviews')->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->text('text');
            $table->softDeletes();
            $table->timestamps();

            $table->index('review_id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};
