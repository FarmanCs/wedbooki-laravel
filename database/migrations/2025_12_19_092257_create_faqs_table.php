<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cms_setting_id')
                ->constrained('cms_settings')
                ->cascadeOnDelete();

            $table->string('question');
            $table->text('answer');
            $table->boolean('is_published')->default(false);
            $table->timestamp('last_updated')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
