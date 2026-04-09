<?php

namespace App;

use App\Enums\GameDificultyEnum;
use App\Enums\SymbolEnum;
use App\Models\Player;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIfunction
{
    private static function getBoard(?int $game_id): array
    {
        $latestPlayer = $game_id
            ? Player::where('game_id', $game_id)->latest()->first()
            : Player::latest()->first();

        if (! $latestPlayer) {
            return array_fill(0, 9, '');
        }

        $board = json_decode($latestPlayer->data, true);

        return (is_array($board) && count($board) === 9)
            ? $board
            : array_fill(0, 9, '');
    }

    private static function getAllowedMoves(array $board): array
    {
        $allowed = [];

        foreach ($board as $i => $cell) {
            if ($cell === '') {
                $allowed[] = $i;
            }
        }

        return $allowed;
    }

    private static function getSystemPrompt(GameDificultyEnum $difficulty): string
    {
        return match ($difficulty) {
            GameDificultyEnum::Easy =>
                'You are a tic-tac-toe AI. Choose one move only from allowed_moves. Return valid JSON only.',

            GameDificultyEnum::Medium =>
                'You are a tic-tac-toe AI. Choose one move only from allowed_moves. Prefer winning moves, otherwise block opponent wins. Return valid JSON only.',

            GameDificultyEnum::Hard =>
                'You are a tic-tac-toe AI. Choose one move only from allowed_moves. Play optimally: win if possible, otherwise block loss, otherwise force the best draw. Return valid JSON only.',
        };
    }

    private static function getUserPrompt(array $board, array $allowedMoves, SymbolEnum $aiSymbol): string
    {
        return json_encode([
            'ai_symbol' => $aiSymbol->value,
            'board' => $board,
            'allowed_moves' => $allowedMoves,
            'response_schema' => [
                'move' => 'integer from allowed_moves',
                'text' => 'short string'
            ],
            'rules' => [
                'Return JSON only',
                'No markdown',
                'No extra text',
                'move must be from allowed_moves'
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    private static function fallbackMove(array $allowedMoves): int
    {
        return $allowedMoves[array_rand($allowedMoves)];
    }

    private static function findWinningMove(array $board, array $allowedMoves, string $symbol): ?int
    {
        $wins = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6],
        ];

        foreach ($allowedMoves as $move) {
            $test = $board;
            $test[$move] = $symbol;
            foreach ($wins as [$a, $b, $c]) {
                if ($test[$a] === $symbol && $test[$b] === $symbol && $test[$c] === $symbol) {
                    return $move;
                }
            }
        }

        return null;
    }

    public static function AImove(GameDificultyEnum $difficulty, SymbolEnum $aiSymbol, ?int $game_id): array
    {
        $board = self::getBoard($game_id);
        $allowedMoves = self::getAllowedMoves($board);

        if (empty($allowedMoves)) {
            return ['error' => 'No valid moves left'];
        }

        if (app()->environment('testing')) {
            $ai = $aiSymbol->value;
            $opponent = $aiSymbol->opposite()->value;
            $move = self::findWinningMove($board, $allowedMoves, $ai)
                ?? self::findWinningMove($board, $allowedMoves, $opponent)
                ?? $allowedMoves[0];

            return [
                'move' => $move,
                'symbol' => $ai,
                'text' => '',
            ];
        }

        $systemPrompt = self::getSystemPrompt($difficulty);
        $userPrompt = self::getUserPrompt($board, $allowedMoves, $aiSymbol);

        $ollamaBaseUrl = rtrim(env('OLLAMA_BASE_URL', 'http://localhost:11434'), '/');
        $ollamaModel = env('OLLAMA_MODEL', 'llama3.1:8b');

        try {
            $response = Http::timeout(15)->post($ollamaBaseUrl . '/api/generate', [
                'model' => $ollamaModel,
                'system' => $systemPrompt,
                'prompt' => $userPrompt,
                'format' => 'json',
                'options' => [
                    'temperature' => 0,
                    'top_p' => 0.3,
                    'num_predict' => 20,
                    'num_ctx' => 256,
                ],
                'stream' => false,
            ]);

            if (! $response->successful()) {
                Log::error('Ollama failed: ' . $response->body());

                return [
                    'move' => self::fallbackMove($allowedMoves),
                    'symbol' => $aiSymbol->value,
                    'text' => '',
                ];
            }

            $content = $response->json('response');

            if (! is_string($content) || $content === '') {
                throw new \Exception('Empty AI response');
            }

            $data = json_decode($content, true);

            // jei modelis sugeneravo šiukšles -> bandom ištraukt JSON
            if (! is_array($data) && preg_match('/\{.*\}/s', $content, $match)) {
                $data = json_decode($match[0], true);
            }

            $move = $data['move'] ?? null;

            // VALIDACIJA (svarbiausia dalis)
            if (! is_int($move) || ! in_array($move, $allowedMoves, true)) {
                $move = self::fallbackMove($allowedMoves);
            }

            return [
                'move' => $move,
                'symbol' => $aiSymbol->value,
                'text' => is_string($data['text'] ?? null) ? $data['text'] : '',
            ];
        } catch (\Throwable $e) {
            Log::error('AI error: ' . $e->getMessage());

            return [
                'move' => self::fallbackMove($allowedMoves),
                'symbol' => $aiSymbol->value,
                'text' => '',
            ];
        }
    }
}