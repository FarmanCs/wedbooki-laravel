<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->json('img')->nullable();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('category');
            $table->softDeletes();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
