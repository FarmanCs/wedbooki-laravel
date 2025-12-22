<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_packages', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor_type', [
                'Photographers',
                'Videographers',
                'Stage Decor',
                'Makeup Artists',
                'Henna Artists',
                'Car Rentals',
                'DJs',
                'Catering',
                'Florists',
                'Bridal Wear',
                'Venues'
            ]);
            $table->enum('package_level', ['Silver', 'Gold', 'Platinum']);
            $table->text('package_description')->nullable();
            $table->json('prices'); // stores durationType and price
            $table->json('package_features')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('vendor_type');
            $table->index('package_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_packages');
    }
};
