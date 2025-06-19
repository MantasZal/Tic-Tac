<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Models\Player;
use App\Models\User;
use App\Models\GameResult;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Achievement;

class PlayerObserver
{
    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        if ($player->gameOver) {
            //Get Loged in User id
            $userId = Auth::id();
            Log::info("Logged-in user ID: " . $userId);

            //Count total wins
            $TotalWins = GameResult::where('user_id', $userId)->where('won', 1)->count();
            Log::info("Total Wins: " . $TotalWins);

            //Count win streak
            $results = GameResult::where('user_id', $userId)->orderBy('id')->get();

            $maxStreak = 0;
            $currentStreak = 0;

            foreach ($results as $result) {
                if ($result->won == 1) {
                    $currentStreak++;
                    $maxStreak = max($maxStreak, $currentStreak);
                } else {
                    $currentStreak = 0; // streak broken
                }
            }
            Log::info("Longest win streak: " . $maxStreak);

            //Count all loses
            $TotalLoses = GameResult::where('user_id', $userId)->where('won', 0)->count();

            //Count all played games
            $Totalgames = GameResult::where('user_id', $userId)->count();

            //Get players Rank
            $rank = auth::User()->rank;

            //Get user ID
            $user = User::find($userId);

            $unlockedAchievements = $user->achievements()->pluck('title')->toArray();

            $newAchievements = [];
            $toUnlockIds = [];

            $allAchievements = Achievement::all();

            foreach ($allAchievements as $achievement) {
                if (in_array($achievement->title, $unlockedAchievements)) {
                    continue;
                }

                $title = $achievement->title;

                if (
                    ($title === "First Win" && $TotalWins >= 1) ||
                    ($title === "Play 10 games" && $Totalgames >= 10) ||
                    ($title === "Reach 50 points" && $rank >= 50) ||
                    ($title === "Win 3 games in a row" && $maxStreak >= 3) ||
                    ($title === "Lose 5 times" && $TotalLoses >= 5)
                ) {
                    $user->achievements()->attach($achievement->id);
                    $newAchievements[] = $title;
                }
            }
            if (!empty($newAchievements)) {
                $latestGame = GameResult::where('user_id', $userId)
                    ->latest()
                    ->first();

                if ($latestGame) {
                    $latestGame->new_acievements = json_encode($newAchievements);
                    $latestGame->save();
                }
            }
        }
    }
}
