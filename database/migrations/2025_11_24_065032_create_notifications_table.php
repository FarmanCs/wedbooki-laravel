<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->enum('type', ['Announcement', 'Alert', 'Reminder'])->nullable();
            $table->enum('recipients', ['Users', 'Vendors', 'All'])->nullable();
            $table->enum('delivery_method', ['Email', 'SMS', 'Push Notification', 'All'])->nullable();
            $table->enum('send_mode', ['Send Immediately', 'Schedule', 'Save as draft'])->default('Send Immediately');
            $table->timestamp('scheduled_at')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
