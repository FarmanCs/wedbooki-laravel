<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('name');
            $table->json('timings')->nullable();
            $table->json('extra_services')->nullable();
            $table->json('images')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('capacity');
            $table->json('available_dates')->nullable();
            $table->enum('status', ['pending', 'rejected', 'active'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
