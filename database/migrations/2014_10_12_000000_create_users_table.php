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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('device_id', 191)->unique()->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_name')->nullable();
            $table->string('ref_id')->nullable();
            $table->string('referral_code')->nullable();
            $table->integer('coins')->nullable();
            $table->integer('subscription_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email', 191)->unique();
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('profile1')->nullable();
            $table->string('profile2')->nullable();
            $table->string('selfie')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('gender_filter')->nullable();
            $table->string('radius_filter')->nullable();
            $table->string('from_age_filter')->nullable();
            $table->string('to_age_filter')->nullable();
            $table->enum('status', ['0', '1'])
                  ->default('1')
                  ->comment('1: active, 0: inactive');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
