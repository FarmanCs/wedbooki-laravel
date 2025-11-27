<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_group_guest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_group_id')->constrained('guest_groups')->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['guest_group_id', 'guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_group_guest');
    }
};
