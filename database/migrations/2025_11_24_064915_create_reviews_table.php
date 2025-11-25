<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->integer('points')->default(1);
            $table->text('text');
            $table->timestamps();

            $table->index('host_id');
            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
