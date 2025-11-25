<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('blog_title')->nullable();
            $table->text('blog_brief_description')->nullable();
            $table->longText('blog_description')->nullable();
            $table->json('author')->nullable();
            $table->string('blog_image')->nullable();
            $table->enum('blog_status', ['draft', 'published'])->default('draft');
            $table->date('published_date')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->onDelete('set null');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->timestamps();

            $table->index('blog_status');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
