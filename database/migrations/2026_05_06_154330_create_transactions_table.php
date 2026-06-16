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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactionable');

            $table->string('note')->nullable();

            $table->decimal('amount', 12, 2);
            $table->decimal('converted_amount', 12, 2);
            $table->string('currency');
            $table->string('converted_currency');
            $table->decimal('exchange_rate', 8, 4);

            $table->string('mode');
            $table->enum('type', ['credit', 'debit']);

            $table->unsignedInteger('tax');
            $table->decimal('tax_amount', 12, 2);

            $table->string('hash')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
