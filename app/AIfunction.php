<?php

namespace App;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Models\Player;

class AIfunction
{
    public static function AImove($board, $difficulty, $aiSymbol)
    {
        $board = json_decode(Player::latest('id')->first()->data);
        Log::info('cqvrqefrcqcFHBCDOihbhdhboHBO', ['$board[$move]' => $board]);

        // Create a comma-separated string representation of the board

        $systemPrompt = match (strtolower($difficulty)) {
            'easy' => "You pick a random valid move. No need to always block or win.",
            'medium' => "You try to block opponent's winning moves and try to win, but you might miss some optimal moves.",
            'hard' => "You are an expert Tic Tac Toe player. You always make the optimal move to win or force a draw.",
        };

        $userPrompt = <<<PROMPT
You are a Tic Tac Toe AI. You are playing as "$aiSymbol".

Tell me the next move (0-8) and also give a short text.

Example empty board with coordinates:
 0 | 1 | 2  
 3 | 4 | 5  
 6 | 7 | 8  

 Playes O goes to cordinate 4 you ca not move to cordinate 0 because it is ocupied by X, Current Board
 X |   |   
   | 0 |   
   |   |  

Respond only in JSON format like this:
{ "move": 4, "text": "i moved to 4 cordinate" }

Only give JSON. Do not explain outside the JSON.
Do not play positions which are already occupied.
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
            Log::info('cqvrqefrcq', ['$board[$move]' => $board]);
            $attempts++;
            Log::info('cqvrqefrcq', [' $attempts' =>  $attempts]);
        } while (($move < -1 || $move > 9 || $board[$move] !== '') && $attempts < 8);

        if (!is_array($data) || !isset($data['move']) || $attempts >= 8) {
            return ['error' => 'Invalid AI response'];
        }

        return [
            'move' => $move,
            'symbol' => $aiSymbol,
            'text' => $data['text'] ?? '',
        ];
    }
}
