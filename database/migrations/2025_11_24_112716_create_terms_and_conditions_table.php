<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('terms_and_conditions', function (Blueprint $table) {
            $table->id();

            // Equivalent to termsAndConditions: String, required
            $table->text('terms_and_conditions');

            // Equivalent to updatedAt: Date
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // If you also want created_at, you can add this manually:
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_and_conditions');
    }
};
