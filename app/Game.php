<?php



namespace App;

use Illuminate\Support\Facades\Log;
use App\Models\Player;

class Game
{
    public static function checkGameOver($aiSymbol, $playerName): array
    {
        $board = json_decode(Player::latest('id')->first()->data);



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
                $winner = $symbol === $aiSymbol ? 'AI' : $playerName;
                Log::info('Board is:', ['winner' => $winner]);

                return [
                    'gameOver' => true,
                    'winner' => $winner,
                    'isDraw' => false,
                ];
            }
        }

        if (!in_array('', $board)) {
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
    public static function savingboard($gameOver, $board, $playerSymbol)
    {
        $gameOver = $gameOver ? 1 : 0;
        $jsonBoard = json_encode($board);

        $player = new Player;
        $player->gameOver = $gameOver;
        $player->data = $jsonBoard;
        $player->player = $playerSymbol;
        $player->save();
    }
}
