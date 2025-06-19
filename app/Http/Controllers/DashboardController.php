<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
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
            'lastAchievement' =>  $decodedAchievements
        ]);
    }
}
