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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('hotel_id');
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->string('reference_number');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('sub_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency')->default('USD');
            $table->string('instructions')->nullable();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->dateTime('arrival')->nullable();
            $table->dateTime('leaved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
