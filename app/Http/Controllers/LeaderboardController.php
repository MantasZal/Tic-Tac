<?php

namespace App\Http\Controllers;

use App\Models\GameResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function storeResult(Request $request)
    {
        GameResult::create([
            'user_id' => Auth::id(),
            'won' => $request->input('won'), // true or false
        ]);

        return redirect()->route('dashboard')->with([
            'success' => 'Game result saved.',
            'last_achievement' => $request->input('achievement') ?? null,
        ]);
    }

    public function index()
    {
        return view('leaderboard');
    }

    public function getLeaderBoard()
    {

        $topUsers = User::orderBy('rank', 'desc')->limit(10)->get();

        return view('leaderboard', compact('topUsers'));
    }
}
