<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('host_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('cascade');
            $table->enum('session_status', ['Initiated', 'Started', 'Completed', 'Cancelled'])->default('Initiated');
            $table->json('params')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamps();

            $table->index('host_id');
            $table->index('session_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('host_sessions');
    }
};
