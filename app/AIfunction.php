<?php

namespace App;

use App\Enums\GameDificultyEnum;
use App\Enums\SymbolEnum;
use App\Models\Player;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public static function AImove(GameDificultyEnum $difficulty, SymbolEnum $aiSymbol, ?int $game_id): array
    {
        $latestPlayer = $game_id
            ? Player::where('game_id', $game_id)->latest()->first()
            : Player::latest()->first();
        if (! $latestPlayer) {
            $board = array_fill(0, 9, '');
        } else {
            $board = json_decode($latestPlayer->data, true);
        }
        if (! is_array($board)) {
            return ['error' => 'Invalid board state'];
        }

        $boardText = self::renderBoard($board);
        log::info(' $difficultyai function ' .  $difficulty->value);

        // Create a comma-separated string representation of the board
        $systemPrompt = match ($difficulty) {
            GameDificultyEnum::Easy  => 'Pick any valid move. Avoid occupied cells.',
            GameDificultyEnum::Medium  => 'Block opponent wins and try to win when possible.',
            GameDificultyEnum::Hard => 'Play optimally. Win if possible, else force a draw.',
        };
        log::info('  $systemPrompt function ' .   $systemPrompt);

        $userPrompt = <<<PROMPT
    You are playing as "{$aiSymbol->value}".
    Return JSON only: {"move": <0-8>, "text": "..."}
    Do not choose occupied cells.
    Board (0-8):
    $boardText
    PROMPT;

        $attempts = 0;
        $move = -1;
        $data = null;
        $ollamaBaseUrl = rtrim(env('OLLAMA_BASE_URL', 'http://localhost:11434'), '/');
        $ollamaModel = env('OLLAMA_MODEL', 'llama3.1:8b');

        do {
            try {
                    $ollamaResponse = Http::timeout(45)->post($ollamaBaseUrl.'/api/generate', [
                    'model' => $ollamaModel,
                    'system' => $systemPrompt,
                    'prompt' => $userPrompt,
                    'format' => 'json',
                    'options' => [
                        'temperature' => 0.2,
                        'top_p' => 0.9,
                            'num_predict' => 64,
                            'num_ctx' => 512,
                    ],
                    'stream' => false,
                ]);

                if (! $ollamaResponse->successful()) {
                    Log::error('Ollama request failed: '.$ollamaResponse->body());
                    return ['error' => 'AI request failed'];
                }

                $content = $ollamaResponse->json('response');
                if (! is_string($content) || $content === '') {
                    return ['error' => 'AI request failed'];
                }

                $data = json_decode($content, true);
                if (! is_array($data)) {
                    if (preg_match('/\{.*\}/s', $content, $match)) {
                        $data = json_decode($match[0], true);
                    }
                }
                $move = $data['move'] ?? -1;
            } catch (\Throwable $error) {
                Log::error('AI request failed: '.$error->getMessage());
                return ['error' => 'AI request failed'];
            }

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
