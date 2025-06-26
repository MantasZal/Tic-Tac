<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AchievementUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $achievementIds = Achievement::pluck('id')->toArray();

        foreach ($users as $user) {
            // Randomly select 1–N achievements
            $randomAchievementIds = collect($achievementIds)->random(rand(1, count($achievementIds)))->toArray();

            // Attach them without removing existing ones
            $user->achievements()->syncWithoutDetaching($randomAchievementIds);
        }
    }
}
