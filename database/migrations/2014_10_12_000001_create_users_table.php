<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('timezone')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default(User::STATUS_ACTIVE);
            $table->dateTime('joining_date')->nullable();
            $table->dateTime('leaving_date')->nullable();
            $table->boolean('can_procure')->default(false);
            $table->float('hourly_rate')->default(0.0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('password_changed')->default(false);
            $table->text('dropbox_access_token')->nullable();
            $table->text('dropbox_refresh_token')->nullable();
            $table->string('sick_leaves')->nullable();
            $table->string('casual_leaves')->nullable();
            $table->string('annual_leaves')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->softDeletes();

            $table->foreignId('company_id')->nullable()->constrained();
            // Add the composite unique index
            $table->unique(['company_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['company_id']);
                
            // Then drop the unique index
            $table->dropUnique(['company_id', 'email']);
        });

        Schema::dropIfExists('users');
    }
};
