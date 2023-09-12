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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('lets_count')->unsigned()->nullable();
            $table->string('validity')->nullable();
            $table->enum('isCarryforward', ['0', '1'])
                ->default('0')
                ->comment('1: true, 0: false');
            $table->enum('isReferralBonus', ['0', '1'])
                ->default('0')
                ->comment('1: true, 0: false');
            $table->enum('isRadiusChange', ['0', '1'])
                ->default('0')
                ->comment('1: true, 0: false');
            $table->enum('isGenderSelect', ['0', '1'])
                ->default('0')
                ->comment('1: true, 0: false');
            $table->enum('isAgeSelect', ['0', '1'])
                ->default('0')
                ->comment('1: true, 0: false');
            $table->enum('status', ['0', '1'])
                ->default('1')
                ->comment('1: active, 0: inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
