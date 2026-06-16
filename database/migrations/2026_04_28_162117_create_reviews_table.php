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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreignId('booking_id')->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->tinyInteger('cleaning')->unsigned()->nullable();
            $table->tinyInteger('services')->unsigned()->nullable();
            $table->tinyInteger('food')->unsigned()->nullable();
            $table->tinyInteger('hospitality')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
