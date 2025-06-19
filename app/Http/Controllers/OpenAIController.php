<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIController extends Controller
{
    public function aiMove(Request $request)
    {
        $board = json_decode($request->board, true);  // Expecting array like ["X", "", "O", ...]
        $aiSymbol = $request->ai_symbol ?? 'O';       // AI plays "O" or "X"
        $difficulty = $request->difficulty ?? 'hard';

        // Normalize all board cells to string
        $board = array_map(fn($cell) => (string) $cell, $board);

        // Create a comma-separated string representation of the board
        $boardString = implode(',', $board);

        $systemPrompt = match (strtolower($difficulty)) {
            'easy' => "You pick a random valid move. No need to always block or win.",
            'medium' => "You try to block opponent's winning moves and try to win, but you might miss some optimal moves.",
            'hard' => "You are an expert Tic Tac Toe player. You always make the optimal move to win or force a draw.",
        };

        $userPrompt = <<<PROMPT
You are a Tic Tac Toe AI. You are playing as "$aiSymbol".
The board is represented as a flat 0-8 array:
$boardString

Tell me the next move (0-8) and also give a short text.
The board looks like this:
(0,1,2
3,4,5
6,7,8).

Respond only in JSON format like this:
{ "move": 4, "text": "do you like losing ?" }

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
            $attempts++;
        } while (($move < 0 || $move > 8 || $board[$move] !== '') && $attempts < 8);

        if (!is_array($data) || !isset($data['move']) || $attempts >= 8) {
            return response()->json(['error' => 'Invalid AI response'], 400);
        }

        return response()->json([
            'move' => $move,
            'symbol' => $aiSymbol,
            'text' => $data['text'] ?? '',
        ]);
    }
}
