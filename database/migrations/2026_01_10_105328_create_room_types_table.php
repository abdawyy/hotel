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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Standard, Deluxe, Suite, etc.
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->integer('max_guests');
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(2);
            $table->integer('total_rooms'); // Total number of rooms of this type
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('price_per_night');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
