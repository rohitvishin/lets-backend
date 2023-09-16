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
        Schema::create('lets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('acceptor_id')->unsigned()->nullable();
            $table->string('event_name')->nullable();
            $table->string('duration')->nullable();
            $table->string('acceptor_selfie')->nullable();
            $table->string('creator_longitude')->nullable();
            $table->string('creator_latitude')->nullable();
            $table->string('acceptor_longitude')->nullable();
            $table->string('acceptor_latitude')->nullable();
            $table->enum('handshake_status', ['0', '1', '2'])
                  ->default('0')
                  ->comment('1: landmark, 2: device');
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
        Schema::dropIfExists('lets');
    }
};
