<?php

// ======================
// SUBSCRIPTIONS (PACKAGES)
// ======================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('badge')->nullable();
            $table->decimal('monthly_price', 15, 2)->nullable();
            $table->decimal('yearly_price', 15, 2)->nullable();
            $table->enum('package_type', ['monthly', 'yearly'])->default('monthly');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
