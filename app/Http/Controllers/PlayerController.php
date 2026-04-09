<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function dashboard(Request $req)
    {
        $player = new Player;
        $player->gameOver = $req->gameOver;
        $player->data = $req->data;
        $player->player = $req->player;
        $player->save();
    }

    public function index(?string $game_id = null)
    {
        $gameId = is_numeric($game_id) ? (int) $game_id : null;
        $player = $game_id
            ? Player::where('game_id', $gameId)->latest()->first()
            : Player::latest()->first();
        $gameOver = $player?->gameOver;
        $data = $player?->data;
        $lastplayer = $player?->player;
        $latestGame = Player::orderBy('game_id', 'desc')->first();
        $game_id = $gameId ?? ($latestGame?->game_id ?? 0);

        return view('dashboard', [
            'gameOver' => $gameOver,
            'data' => $data,
            'lastplayer' => $lastplayer,
            'game_id' => $game_id,
        ]);
    }
    public function save_game_id(Request $req)
    {
        $player = new Player;
        $player->gameOver = $req->gameOver;
        $player->data = $req->data;
        $player->player = $req->player;
        $player->save();
    }

    public function updateByGameId(Request $request, int $game_id)
    {
        $clientVersion = $request->header('X-Client-Version');
        if (! $clientVersion) {
            return response()->json([
                'message' => 'X-Client-Version header required',
            ], 400);
        }

        $payload = $request->validate([
            'gameOver' => 'required|boolean',
            'data' => 'required|string',
            'player' => 'required|string|max:20',
        ]);

        $player = Player::where('game_id', $game_id)->latest()->first();
        $isNew = false;

        if (! $player) {
            $player = new Player;
            $player->game_id = $game_id;
            $isNew = true;
        }

        $player->gameOver = $payload['gameOver'];
        $player->data = $payload['data'];
        $player->player = $payload['player'];
        $player->save();

        return response()->json([
            'game_id' => $game_id,
            'client_version' => $clientVersion,
            'created' => $isNew,
            'player_id' => $player->id,
        ], $isNew ? 201 : 200);
    }

    public function deleteByGameId(int $game_id)
    {
        $deleted = Player::where('game_id', $game_id)->delete();

        if ($deleted === 0) {
            return response()->json([
                'message' => 'No records found for game_id',
            ], 404);
        }

        return response()->json([
            'game_id' => $game_id,
            'deleted' => $deleted,
        ]);
    }
}
