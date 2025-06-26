<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user) {
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
