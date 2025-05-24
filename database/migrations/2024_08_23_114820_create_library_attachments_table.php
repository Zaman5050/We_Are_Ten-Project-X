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
        Schema::create('library_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('attachmentable_id')->nullable();
            $table->string('attachmentable_type')->nullable();
            $table->enum('label',['product_image','product_file'])->default('product_file');
            $table->string('original_name')->nullable();
            $table->string('path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_attachments');
    }
};
