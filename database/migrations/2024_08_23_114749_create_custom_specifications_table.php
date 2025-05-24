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
        Schema::create('custom_specifications', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('specificationable_id')->nullable();
            $table->string('specificationable_type')->nullable();
            $table->string('label')->nullable();
            $table->text('custom_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_specifications');
    }
};
