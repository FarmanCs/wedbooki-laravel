<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('cascade');
            $table->date('wedding_date')->nullable();
            $table->json('checklist_items')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('host_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
