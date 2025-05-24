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
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('project_task_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->boolean('is_automatic')->default(1);
            $table->timestamp('start_time')->default(now());
            $table->timestamp('end_time')->nullable();
            $table->string('total_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
