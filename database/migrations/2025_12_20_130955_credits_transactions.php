<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credits_transactions', function (Blueprint $table) {
            $table->id();

            // Foreign key to businesses table
            $table->foreignId('business_id')
                ->constrained('businesses')
                ->onDelete('cascade');

            $table->integer('no_of_credits')->default(0);
            $table->decimal('amount', 10, 2)->default(0);

            $table->string('from')->nullable();
            $table->string('to')->nullable();

            $table->enum('tran_type', ['credit', 'debit']);

            $table->timestamps(); // includes created_at & updated_at
            $table->softDeletes(); // adds deleted_at for soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credits_transactions');
    }
};
