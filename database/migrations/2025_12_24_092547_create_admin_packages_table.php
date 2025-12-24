<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Package name like "Venue", "Photography", etc.

            // Silver Tier
            $table->text('silver_description')->nullable();
            $table->string('silver_badge')->nullable();
            $table->decimal('silver_monthly_price', 10, 2);
            $table->decimal('silver_quarterly_price', 10, 2)->nullable();
            $table->decimal('silver_yearly_price', 10, 2)->nullable();

            // Gold Tier
            $table->text('gold_description')->nullable();
            $table->string('gold_badge')->nullable();
            $table->decimal('gold_monthly_price', 10, 2);
            $table->decimal('gold_quarterly_price', 10, 2)->nullable();
            $table->decimal('gold_yearly_price', 10, 2)->nullable();

            // Platinum Tier
            $table->text('platinum_description')->nullable();
            $table->string('platinum_badge')->nullable();
            $table->decimal('platinum_monthly_price', 10, 2);
            $table->decimal('platinum_quarterly_price', 10, 2)->nullable();
            $table->decimal('platinum_yearly_price', 10, 2)->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_packages');
    }
};
