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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount', 15, 6)->change();
            $table->decimal('converted_amount', 15, 6)->change();
            $table->decimal('exchange_rate', 15, 6)->change();
            $table->decimal('tax', 8, 4)->change();
            $table->decimal('tax_amount', 15, 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
