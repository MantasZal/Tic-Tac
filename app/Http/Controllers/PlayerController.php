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

    public function index()
    {
        $player = Player::latest()->first();
        $gameOver = $player?->gameOver;
        $data = $player?->data;
        $lastplayer = $player?->player;
        $game_id = Player::orderBy('game_id', 'desc')->first()->game_id;

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
}
