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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function gamelogic(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0|max:8',
            'gameOver' => 'nullable|in:0,1,true,false',
            'aisymbol' => 'sometimes|in:X,O',
            'playerNameFromServer' => 'sometimes|string|max:50',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'ai_enabled' => 'sometimes|boolean',
            'current' => 'sometimes|in:X,O',
            'game_id' => 'sometimes|integer|min:0',
        ]);

        $userId = Auth::id();
        if (! $userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $index = $request->input('index') ?? 0;
        $gameOver = filter_var(
            $request->input('gameOver', false),
            FILTER_VALIDATE_BOOLEAN
        );
        $aiSymbol = SymbolEnum::from($request->aisymbol ?? 'O');
        $playerName = $request->input('playerNameFromServer');
        $difficulty = GameDificultyEnum::from($request->difficulty ?? 'hard');
        $aiEnabled = filter_var($request->input('ai_enabled', true), FILTER_VALIDATE_BOOL);
        $currentSymbol = SymbolEnum::from($request->input('current') ?? $aiSymbol->opposite()->value);
        $playerSymbol = $aiEnabled ? $aiSymbol->opposite() : $currentSymbol;
        $user = User::find($userId);
        $game_id = $request->game_id;
        $latestPlayer = $game_id
            ? Player::where('game_id', $game_id)->latest()->first()
            : Player::latest()->first();
        $board = $latestPlayer ? json_decode($latestPlayer->data, true) : array_fill(0, 9, '');
        $game_id = $game_id ?? ($latestPlayer?->game_id ?? 0);

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

        if ($aiEnabled && ! in_array('X', $board, true) && $aiSymbol->value === 'X') {

            $AIresult = AIfunction::AImove($difficulty, $aiSymbol, $game_id);
            if (isset($AIresult['error'])) {
                return response()->json([
                    'error' => $AIresult['error'],
                    'board' => $board,
                ], 503);
            }
            $AIresult['AImove'] = $AIresult['move'] + 1;
            $board[$AIresult['move']] = $aiSymbol->value;
            $result['message'] = $AIresult['text'];
            Game::savingboard($gameOver, $board, $playerSymbol, $game_id);
            $AIresult['board'] = $board;

            return response()->json($AIresult);
        } else {
            // Checking if player move is valid

            // Player making move
            $board[$index] = $playerSymbol->value;

            // Saving board
            Game::savingboard($gameOver, $board, $playerSymbol, $game_id);

            // Checking for a winner
            $aiName = $aiEnabled ? 'AI' : 'Player 2';
            $result = Game::checkGameOver($aiSymbol, $playerName, $game_id, $aiName, $board);
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
            if (! $aiEnabled) {
                return response()->json($result);
            }

            $AIresult = AIfunction::AImove($difficulty, $aiSymbol, $game_id);
            if (isset($AIresult['error'])) {
                return response()->json([
                    'error' => $AIresult['error'],
                    'board' => $board,
                ], 503);
            }
            $board[$AIresult['move']] = $aiSymbol->value;
            $AIresult['AImove'] = $AIresult['move'] + 1;
            $result['AImove'] = $AIresult['move'] + 1;
            $result2['AImove'] = $AIresult['move'] + 1;

            // Saving board
            Game::savingboard($gameOver, $board, $aiSymbol, $game_id);
            $result['board'] = $board;
            $result['message'] = $AIresult['text'];

            $result2 = Game::checkGameOver($aiSymbol, $playerName, $game_id, 'AI', $board);
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
        $payload = $req->validate([
            'starter' => 'required|in:human,ai',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $gametable = new GameTable;
        $gametable->starter = $payload['starter'];
        $gametable->difficulty = $payload['difficulty'];
        $gametable->save();
        $game_id = $gametable->id;
        return response()->json(['game_id' => $gametable->id]);
    }
}
