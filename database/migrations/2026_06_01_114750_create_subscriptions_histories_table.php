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
        Schema::create('subscriptions_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 16, 4);
            $table->string('currency')->default('USD');
            $table->decimal('converted_amount', 16, 4);
            $table->string('converted_currency');
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_price_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions_histories');
    }
};
