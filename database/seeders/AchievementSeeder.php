<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run()
    {
        $achievements = [
            ['title' => 'First Win', 'description' => 'Win your first game', 'icon' => 'fa-trophy'],
            ['title' => 'Play 10 games', 'description' => 'Play a total of 10 games', 'icon' => 'fa-gamepad'],
            ['title' => 'Reach 50 points', 'description' => 'Accumulate 50 points', 'icon' => 'fa-star'],
            ['title' => 'Win 3 games in a row', 'description' => 'Win 3 consecutive games', 'icon' => 'fa-fire'],
            ['title' => 'Lose 5 times', 'description' => 'Lose 5 games', 'icon' => 'fa-skull-crossbones'],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
