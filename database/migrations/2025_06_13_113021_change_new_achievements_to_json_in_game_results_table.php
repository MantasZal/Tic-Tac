<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('game_results', function (Blueprint $table) {
            // Change 'new_acievements' from string to json
            $table->json('new_acievements')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('game_results', function (Blueprint $table) {
            // Revert back to string if needed
            $table->string('new_acievements')->nullable()->change();
        });
    }
};
