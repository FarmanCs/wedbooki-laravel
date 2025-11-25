<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->json('participants'); // stores userId, userModel
            $table->unsignedBigInteger('last_message_id')->nullable(); // Don't add foreign key yet
            $table->timestamps();

            $table->index('last_message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
