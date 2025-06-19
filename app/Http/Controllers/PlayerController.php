<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    function dashboard(Request $req)
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

        return view('dashboard', [
            'gameOver' => $gameOver,
            'data' => $data,
            'lastplayer' => $lastplayer,
        ]);
    }
}
