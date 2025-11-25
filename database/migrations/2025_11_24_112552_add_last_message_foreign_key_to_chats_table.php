<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Now add the foreign key constraint after messages table exists
            $table->foreign('last_message_id')
                ->references('id')
                ->on('messages')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
        });
    }
};
