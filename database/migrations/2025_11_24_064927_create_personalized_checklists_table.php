<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personalized_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->string('check_list_title')->nullable();
            $table->string('check_list_category')->nullable();
            $table->text('check_list_description')->nullable();
            $table->date('check_list_due_date')->nullable();
            $table->enum('checklist_status', ['pending', 'checked'])->nullable();
            $table->string('check_list_item_linked_with')->nullable();
            $table->string('check_list_item_linked_with_id')->nullable();
            $table->string('checklist_linked_booking_id')->nullable();
            $table->string('checklist_linked_booking')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->boolean('lock_to_wedding_date')->nullable();
            $table->timestamps();

            $table->index('host_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personalized_checklists');
    }
};
