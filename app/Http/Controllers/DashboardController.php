<?php

namespace App\Http\Controllers;

use App\Models\SaveGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function save_game_results(Request $req)
    {
        $userId = Auth::id();
        if (! $userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $req->validate([
            'won' => 'required|boolean',
        ]);

        $SaveGame = new SaveGame;
        $SaveGame->user_id = $userId;
        $SaveGame->won = $payload['won'];

        $SaveGame->save();
    }

    public function index()
    {
        $user = Auth::user();

        $latestGameResult = DB::table('game_results')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $newAchievements = $latestGameResult ? $latestGameResult->new_acievements : null;
        $decodedAchievements = $newAchievements ? json_decode($newAchievements, true) : [];

        return response()->json([
            'lastAchievement' => $decodedAchievements,
        ]);
    }
}
