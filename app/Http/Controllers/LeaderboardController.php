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

    public function apiLeaderboard(Request $request)
    {
        $limit = (int) $request->query('limit', 10);

        if ($limit < 1 || $limit > 50) {
            return response()->json([
                'message' => 'limit must be between 1 and 50',
            ], 400);
        }

        $topUsers = User::orderBy('rank', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'rank']);

        return response()->json([
            'limit' => $limit,
            'data' => $topUsers,
        ]);
    }
}
