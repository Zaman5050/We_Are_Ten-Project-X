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
        Schema::create('material_libraries', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('supplier_library_id')->nullable()->constrained();
            $table->string('item_name')->nullable();
            $table->string('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('doc_code')->nullable();
            $table->string('product_url')->nullable();
            $table->string('height')->nullable();
            $table->string('depth')->nullable();
            $table->string('price')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->string('color')->nullable();
            $table->string('finish')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_libraries');
    }
};
