<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\User;
use App\Game;
use App\AIfunction;

class GameController extends Controller
{
    public function gamelogic(Request $request)
    {
        $index = $request->input('index') ?? 0;
        $gameOver = $request->input('gameOver') ?? 0;
        $aiSymbol = $request->input('aisymbol') ?? 'O';
        $playerName = $request->input('playerNameFromServer') ?? 'Kaput';
        $userId = $request->input('id') ?? 5;
        $board = json_decode(Player::latest('id')->first()->data);
        $difficulty = $request->difficulty ?? 'hard';
        $playerSymbol = $aiSymbol === "X" ? "O" : "X";
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        Log::info('Board is:', ['$board' => $board]);
        Log::info('Board is:', ['aisymbol' => $aiSymbol]);
        if (empty($board) && $aiSymbol === "X") {
            $AIresult = AIfunction::AImove($board, $difficulty, $aiSymbol);
            $board[$AIresult['move']] = $aiSymbol;
            $result['message'] = $AIresult['text'];
            Log::info('Board is:vdav cui acy wa9yc', ['$board' => $board]);

            return response()->json($AIresult);
        }
        //Checking if player move is valid 
        if (!is_array($board) || $board[$index] !== "") {
            return response()->json([
                'valid' => false,
            ]);
        } else {





            //Player making move 
            $board[$index] = $playerSymbol;

            //Saving board
            Game::savingboard($gameOver, $board, $playerSymbol);



            //Checking for a winner
            $result = Game::checkGameOver($aiSymbol, $playerName);
            $result['board'] = $board;
            if ($result['gameOver']) {
                $change = $result['winner'] === "AI" ? -3 : 5;
                $user->rank += $change;
                $user->save();
                $result['new_rank'] = $user->rank;
                $result['message'] = "Conracualtions";
                return response()->json($result);
            }


            //AI move
            $AIresult = AIfunction::AImove($board, $difficulty, $aiSymbol);
            $board[$AIresult['move']] = $aiSymbol;

            //Saving board
            Game::savingboard($gameOver, $board, $aiSymbol);
            $result['board'] = $board;
            $result['message'] = $AIresult['text'];


            $result2 = Game::checkGameOver($aiSymbol, $playerName);
            $result2['board'] = $board;
            if ($result2['gameOver']) {
                $change = $result2['winner'] === "AI" ? -3 : 5;
                $user->rank += $change;
                $user->save();
                $result2['new_rank'] = $user->rank;
                $result['message'] = "Conracualtions";
                return response()->json($result2);
            }

            return response()->json($result);
        }
    }
}
