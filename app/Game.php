<?php

namespace App;

use App\Enums\SymbolEnum;
use App\Models\Player;
use Illuminate\Support\Facades\Log;

class Game
{
    public static function checkGameOver(SymbolEnum $aiSymbol, string $playerName, ?int $game_id, string $aiName = 'AI'): array
    {
        $player = $game_id
            ? Player::where('game_id', $game_id)->latest()->first()
            : Player::latest()->first();

        if (! $player) {
            return [
                'gameOver' => 0,
                'winner' => null,
                'isDraw' => false,
            ];
        }

        $board = json_decode($player->data, true);

        $wins = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8], // rows
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8], // cols
            [0, 4, 8],
            [2, 4, 6],            // diagonals
        ];

        foreach ($wins as [$a, $b, $c]) {
            if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
                $symbol = $board[$a];
                $winner = $symbol === $aiSymbol ? $aiName : $playerName;

                return [
                    'gameOver' => true,
                    'winner' => $winner,
                    'isDraw' => false,
                ];
            }
        }

        if (! in_array('', $board)) {
            return [
                'gameOver' => true,
                'winner' => null,
                'isDraw' => true,
            ];
        }

        return [
            'gameOver' => 0,
            'winner' => null,
            'isDraw' => false,
        ];
    }

    public static function savingBoard(bool $gameOver, array $board, SymbolEnum $playerSymbol, ?int $game_id = null): Player
    {
        // $gameOver = $gameOver ? 1 : 0;
        $jsonBoard = json_encode($board);

        $player = new Player;
        $player->gameOver = $gameOver;
        $player->data = $jsonBoard;
        $player->player = $playerSymbol->value;
        if ($game_id) {
            $player->game_id = $game_id;
        }
        $player->save();

        return $player;
    }
}
