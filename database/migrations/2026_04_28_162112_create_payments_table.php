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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id');
            $table->string('gateway')->default('stripe');
            $table->decimal('amount', 15, 2);
            $table->decimal('converted_amount', 15, 2)->nullable();
            $table->string('paid_currency', 10)->nullable();
            $table->decimal('exchange_rate', 12, 8)->nullable()->comment('from user currency to hotel currency');
            $table->string('currency', 10)->default('USD');
            $table->tinyInteger('status')->default(0)->comment('0:pending, 1:success, 2:failed, 3:processing');
            $table->string('session_id')->nullable()->unique();
            $table->string('payment_intent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
