<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->json('sender'); // stores userId, userModel
            $table->text('text')->nullable();
            $table->string('chat_image_url')->nullable();
            $table->string('chat_video_url')->nullable();
            $table->string('chat_document_url')->nullable();
            $table->json('seen_by')->nullable();
            $table->timestamps();

            $table->index('chat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
