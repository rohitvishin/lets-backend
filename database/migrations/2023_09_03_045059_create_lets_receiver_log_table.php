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
        Schema::create('lets_receiver_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('lets_id')->nullable();
            $table->string('user_longitude')->nullable();
            $table->string('user_latitude')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lets_receiver_log');
    }
};
