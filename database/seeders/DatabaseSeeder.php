<?php

namespace Database\Seeders;

use App\Models\Player;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        player::create([
            'data' => json_encode(["", "", "", "", "", "", "", "", ""]), // empty board
            'player' => 'X',
            'gameOver' => false,
        ]);
        User::factory()->count(50)->create();
    }
}
