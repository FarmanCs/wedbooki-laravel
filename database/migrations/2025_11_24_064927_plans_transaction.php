<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->onDelete('cascade');

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->onDelete('cascade');

            $table->timestamp('tran_time')->useCurrent();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('tran_type', ['purchase', 'renewal']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans_transactions');
    }
};
