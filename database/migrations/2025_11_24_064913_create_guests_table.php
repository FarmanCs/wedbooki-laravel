<?php

// ======================
// GUESTS
// ======================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('full_name')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->enum('is_joining', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->softDeletes();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
