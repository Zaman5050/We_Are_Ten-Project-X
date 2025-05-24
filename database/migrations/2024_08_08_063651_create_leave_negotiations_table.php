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
        Schema::create('leave_negotiations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('leave_id')->constrained('user_leaves')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('negotiation_type', ['negotiated', 're-negotiated'])->default('negotiated');
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('number_of_days');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_negotiations');
    }
};
