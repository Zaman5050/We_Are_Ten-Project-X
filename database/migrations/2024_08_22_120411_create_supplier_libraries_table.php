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
        Schema::create('supplier_libraries', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->string('company_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->nullable();
            $table->string('trade_discount')->nullable();
            $table->string('website')->nullable();
            $table->string('profile_picture')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_libraries');
    }
};
