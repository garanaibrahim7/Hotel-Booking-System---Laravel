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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 16, 4);
            $table->string('currency')->default('USD');
            $table->string('currency_symbol')->default('$');
            $table->string('stripe_price_id')->unique();
            $table->string('stripe_product_id')->unique();
            $table->json('facilities')->nullable();
            $table->enum('type', ['monthly', '3 months', '6 months', 'yearly', 'lifetime']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
