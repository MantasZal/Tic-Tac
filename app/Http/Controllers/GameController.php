<?php

namespace App\Http\Controllers;

use App\AIfunction;
use App\Enums\GameDificultyEnum;
use App\Enums\SymbolEnum;
use App\Game;
use App\Models\GameTable;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function gamelogic(Request $request)
    {

        $index = $request->input('index') ?? 0;
        $gameOver = $request->gameOver ?? false;
        $aiSymbol = SymbolEnum::from($request->aisymbol ?? 'O');
        $playerName = $request->input('playerNameFromServer');
        $userId = $request->input('id') ?? 5;
        $difficulty = GameDificultyEnum::from($request->difficulty ?? 'hard');
        $playerSymbol = $aiSymbol->opposite();
        $user = User::find($userId);
        $game_id = $request->game_id;
        $board = json_decode(Player::where('game_id', $game_id)->latest()->first()->data);

        log::info('game id ' . $game_id);

        if (! is_array($board) || $board[$index] !== '') {
            return response()->json([
                'invalid' => true,
                'board' => $board,
            ]);
        }

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (! in_array('X', $board, true) && $aiSymbol->value === 'X') {

            $AIresult = AIfunction::AImove($difficulty, $aiSymbol, $game_id);
            $AIresult['AImove'] = $AIresult['move'] + 1;
            $board[$AIresult['move']] = $aiSymbol->value;
            $result['message'] = $AIresult['text'];
            Game::savingboard($gameOver, $board, $playerSymbol);
            $AIresult['board'] = $board;

            return response()->json($AIresult);
        } else {
            // Checking if player move is valid

            // Player making move
            $board[$index] = $playerSymbol->value;

            // Saving board
            Game::savingboard($gameOver, $board, $playerSymbol);

            // Checking for a winner
            $result = Game::checkGameOver($aiSymbol, $playerName, $game_id);
            $result['board'] = $board;
            if ($result['gameOver']) {
                $change = $result['winner'] === 'AI' ? -3 : 5;
                $user->rank += $change;
                $user->save();
                $result['new_rank'] = $user->rank;
                $result['message'] = 'Conracualtions';

                return response()->json($result);
            }

            // AI move
            $AIresult = AIfunction::AImove($difficulty, $aiSymbol, $game_id);
            $board[$AIresult['move']] = $aiSymbol->value;
            $AIresult['AImove'] = $AIresult['move'] + 1;
            $result['AImove'] = $AIresult['move'] + 1;
            $result2['AImove'] = $AIresult['move'] + 1;

            // Saving board
            Game::savingboard($gameOver, $board, $aiSymbol);
            $result['board'] = $board;
            $result['message'] = $AIresult['text'];

            $result2 = Game::checkGameOver($aiSymbol, $playerName, $game_id);
            $result2['board'] = $board;

            if ($result2['gameOver']) {
                $change = $result2['winner'] === 'AI' ? -3 : 5;
                $user->rank += $change;
                $user->save();
                $result2['new_rank'] = $user->rank;
                $result['message'] = 'Conracualtions';

                return response()->json($result2);
            }

            return response()->json($result);
        }
    }
    public function startGame(Request $req)
    {
        $gametable = new GameTable;
        $gametable->starter = $req->starter;
        $gametable->difficulty = $req->difficulty;
        $gametable->save();
        $game_id = $gametable->id;
        return response()->json(['game_id' => $gametable->id]);
    }
}
