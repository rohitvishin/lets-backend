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
            $table->string('creator_selfie')->nullable();
            $table->decimal('creator_longitude', 10, 8)->nullable();
            $table->decimal('creator_latitude', 10, 8)->nullable();
            $table->decimal('acceptor_longitude', 10, 8)->nullable();
            $table->decimal('acceptor_latitude', 10, 8)->nullable();
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
