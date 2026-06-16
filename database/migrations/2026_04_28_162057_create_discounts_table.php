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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('coupen_code');
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 8, 2);
            $table->boolean('required_code')->default(true);
            $table->string('message')->nullable();
            $table->string('hotel_id')->nullable(); // Set as string as it was varchar in your dump instead of BigInt
            $table->boolean('active_status')->default(true);
            $table->dateTime('starts_from')->default('2026-04-02 00:00:00');
            $table->dateTime('ends_at');
            $table->integer('min_nights')->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->integer('user_limit')->nullable();
            $table->integer('min_amount')->default(0);
            $table->decimal('max_discount', 8, 2)->default(0.00)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
