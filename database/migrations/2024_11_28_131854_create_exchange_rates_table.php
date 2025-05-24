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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->unsignedBigInteger('base_currency_id')->nullable();
            $table->unsignedBigInteger('quote_currency_id')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('base_currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('quote_currency_id')->references('id')->on('currencies')->onDelete('cascade');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
