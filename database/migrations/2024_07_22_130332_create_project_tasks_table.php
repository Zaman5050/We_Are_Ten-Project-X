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
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('stage_id');
            $table->string('task_code');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('flaged_by')->nullable();
            $table->boolean('archive')->default(false);
            $table->unsignedBigInteger('status_id');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->string('estimate_time')->nullable();
            $table->integer('order_number')->default(0);
            $table->boolean('is_timer_paused')->default(false);
            $table->enum('timer_mode', ['idle','active','paused']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('stage_id')->references('id')->on('project_stages');
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
