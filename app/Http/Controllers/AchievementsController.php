<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AchievementsController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $userAchievements = $user->achievements;
        $userAchievementIds = $userAchievements->pluck('id');

        $LockedAchievements = Achievement::whereNotIn('id', $userAchievementIds)->get();

        return view('achievements.index', [
            'userAchievements' => $userAchievements,
            'LockedAchievements' => $LockedAchievements, // now this is "not yet achieved"
        ]);
    }
    public function awardAchievement(Request $request)
    {
        $achievementId = $request->input('achievement_id');
        $userId = $request->input('user_id');

        $user = User::findOrFail($userId);
        $achievement = Achievement::findOrFail($achievementId);

        $user->achievements()->syncWithoutDetaching([$achievement->id]);

        return back()->with('success', 'Achievement awarded!');
    }
}
