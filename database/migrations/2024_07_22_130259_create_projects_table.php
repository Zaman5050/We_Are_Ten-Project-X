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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->string('code');
            $table->string('address')->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('type');
            $table->string('measurement_unit')->nullable();
            $table->enum('status', ['active','completed','delayed','archieved','draft'])->default('active');
            $table->text('description')->nullable();
            $table->tinyInteger('is_procurement_enable')->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('owner_id'); // Foreign key column
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
      

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
