<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_services', function (Blueprint $table) {
            $table->id();

            // Optional relationships
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable();

            // Extra service info
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_services');
    }
};
