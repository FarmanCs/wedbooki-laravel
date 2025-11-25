<?php

// ======================
// BUDGETS
// ======================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('cascade');
            $table->decimal('total_budget', 15, 2)->nullable();
            $table->json('breakdown')->nullable(); // stores categoryId and amount
            $table->timestamps();

            $table->index('host_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
