<?php

namespace App;

use App\Enums\GameDificultyEnum;
use App\Enums\SymbolEnum;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AIfunction
{
    private static function renderBoard(array $board): string
    {
        $output = '';
        for ($i = 0; $i < 9; $i++) {
            $cell = $board[$i] === '' ? $i : $board[$i];
            $output .= " $cell ";
            if ($i % 3 !== 2) {
                $output .= '|';
            }
            if ($i % 3 === 2 && $i !== 8) {
                $output .= "\n-----------\n";
            }
        }

        return $output;
    }

    public static function AImove(GameDificultyEnum $difficulty, SymbolEnum $aiSymbol, int $game_id): array
    {

        $board = json_decode(Player::where('game_id', $game_id)->latest()->first()->data);
        $boardText = self::renderBoard($board);
        log::info(' $difficultyai function ' .  $difficulty->value);

        // Create a comma-separated string representation of the board
        $systemPrompt = match ($difficulty) {
            GameDificultyEnum::Easy  => 'You pick a random valid move. No need to always block or win.',
            GameDificultyEnum::Medium  => "You try to block opponent's winning moves and try to win, but you might miss some optimal moves.",
            GameDificultyEnum::Hard => "You are a master Tic-Tac-Toe strategist, trained in game theory and unbeatable logic. You always play optimally — prioritizing a win, blocking threats, and forcing draws if victory isn't possible. Whether you're X or O, you calculate every move to maximize your chances and minimize your opponent's. Never make random or suboptimal moves. Analyze the current board, assess all possibilities, and respond with the best next move, including your reasoning. Your goal is simple: never lose.",
        };
        log::info('  $systemPrompt function ' .   $systemPrompt);

        $userPrompt = <<<PROMPT
You are a Tic Tac Toe AI. You are playing as "{$aiSymbol->value}".

Tell me the next move (0-8) and also give a short text.

Example empty board with coordinates:
 0 | 1 | 2  
 3 | 4 | 5  
 6 | 7 | 8  

 Playes O goes to cordinate 4 you ca not move to cordinate 0 because it is ocupied by X, Current Board
 X |   |   
   | 0 |   
   |   |  
Occupied places from example are 0 and 4 do not play them.

Respond only in JSON format like this:
{ "move": 4, "text": "i moved to 4 cordinate" }

Only give JSON. Do not explain outside the JSON.
Do not play positions which are already occupied by "X" or "O".

Here is the current board:
$boardText
PROMPT;

        $attempts = 0;
        $move = -1;
        $data = null;

        do {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],

            ]);

            $content = $response->choices[0]->message->content;
            $data = json_decode($content, true);
            $move = $data['move'] ?? -1;
            $attempts++;
        } while (($move < 0 || $move > 8 || $board[$move] !== '') && $attempts < 8);

        if (! is_array($data) || ! isset($data['move']) || $attempts >= 8) {
            return ['error' => 'Invalid AI response'];
        }

        return [
            'move' => $move,
            'symbol' => $aiSymbol->value,
            'text' => $data['text'] ?? '',
        ];
    }
}
