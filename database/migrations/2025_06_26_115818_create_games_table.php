<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id(); // Primary key and auto-increment
            $table->string('starter')->nullable();
            $table->string('difficulty')->nullable();
            $table->timestamps();
        });
        Schema::table('players', function (Blueprint $table) {

            $table->unsignedBigInteger('game_id')->nullable()->after('id');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
