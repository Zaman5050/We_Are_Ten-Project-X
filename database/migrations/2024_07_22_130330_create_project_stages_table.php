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
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('stage_name');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('status', ['active','completed','delayed','inactive'])->default('inactive');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
