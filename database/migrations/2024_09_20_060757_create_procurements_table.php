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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_library_id')->nullable()->constrained();
            $table->foreignId('project_area_id')->nullable()->constrained();
            $table->string('quantity')->nullable();
            $table->string('quote_currency_id')->nullable();
            $table->string('base_currency_id')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->string('retail_price')->nullable();
            $table->string('trade_discount')->nullable();
            $table->string('trade_price')->nullable();
            $table->string('actual_price')->nullable();
            $table->string('trade_terms')->nullable();
            $table->string('production_lead_time')->nullable();
            $table->string('shipping_lead_time')->nullable();
            $table->date('order_date')->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('markup')->nullable();
            $table->string('total_exclusive_tax')->nullable();
            $table->string('total_inclusive_tax')->nullable();
            $table->string('profit')->nullable();
            $table->string('status')->default('Draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
