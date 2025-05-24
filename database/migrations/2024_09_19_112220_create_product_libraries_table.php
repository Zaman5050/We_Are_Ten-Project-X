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
        Schema::create('product_libraries', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('supplier_library_id')->nullable()->constrained();
            $table->string('product_name')->nullable();
            $table->string('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('product_url')->nullable();
            $table->string('height')->nullable();
            $table->string('depth')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->string('color')->nullable();
            $table->string('finish')->nullable();
            $table->string('material')->nullable();
            $table->text('notes')->nullable();
            $table->string('retail_price')->nullable();
            $table->string('trade_discount')->nullable();
            $table->string('trade_price')->nullable();
            $table->string('actual_price')->nullable();
            $table->string('trade_terms')->nullable();
            $table->string('production_lead_time')->nullable();
            $table->string('shipping_lead_time')->nullable();
            $table->string('markup')->nullable();
            $table->string('profit')->nullable();
            $table->string('quote_currency_id')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_libraries');
    }
};
