<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');

        if (empty($this->apiKey)) {
            throw new RuntimeException('Gemini API key is not configured. Set GEMINI_API_KEY in your .env file.');
        }
    }

    /**
     * Analyze a chess game from PGN and return structured data.
     *
     * @throws RuntimeException
     */
    public function analyzeGame(string $pgn, string $player): array
    {
        $prompt = $this->buildPrompt($pgn, $player);

        try {
            // To get the models if needed
            //$response = Http::timeout(45)->get("https://generativelanguage.googleapis.com/v1beta/models?key={$this->apiKey}");
            //$content = $response->body(); 
            $response = Http::timeout(45)
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.4,
                        'maxOutputTokens' => 5000,
                    ],
                ]);

            $response->throw(); // Throws RequestException on 4xx/5xx
        } catch (RequestException $e) {
            $msg = $e->response->json('error.message') ?? $e->getMessage();
            throw new RuntimeException("Gemini API error: {$msg}");
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (empty($text)) {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $this->parseJsonResponse($text);
    }

    /**
     * Strip markdown fences and parse the JSON Gemini returns.
     */
    private function parseJsonResponse(string $text): array
    {
        // Gemini sometimes wraps JSON in ```json ... ``` — strip it
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $clean = preg_replace('/\s*```$/', '', $clean);

        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed to parse Gemini response as JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }

    private function buildPrompt(string $pgn, string $player): string
    {
        return <<<PROMPT
You are an expert chess coach analyzing a game provided in PGN format. Analyze the following chess game thoroughly and provide structured feedback.

PGN:
{$pgn}

Respond ONLY with a valid JSON object (no markdown, no code blocks) in this exact structure:
The player is playing the game as {$player}, so your response should be generated for them.
{
  "game_summary": "2-3 sentence overview of the game's character and result",
  "opening": {
    "name": "Name of the opening played",
    "evaluation": "Brief evaluation of how well the opening was handled"
  },
  "pros": [
    "Specific positive aspect",
    "Another positive aspect",
    "Another positive aspect"
  ],
  "cons": [
    "Specific weakness or mistake",
    "Another weakness",
    "Another weakness"
  ],
  "strengths": [
    {
      "title": "Strength category (e.g. Tactical Vision, Endgame Technique)",
      "description": "Detailed explanation of this strength as demonstrated across the game"
    },
    {
      "title": "Second strength category",
      "description": "Detailed explanation of this second strength"
    }
  ],
  "weaknesses": [
    {
      "title": "Weakness category (e.g. Opening Preparation, Positional Play)",
      "description": "Detailed explanation and concrete improvement advice"
    },
    {
      "title": "Second weakness category",
      "description": "Detailed explanation and concrete improvement advice"
    }
  ],
  "key_moments": [
    {
      "move": "Move notation (e.g. 24. Rxe6)",
      "significance": "Why this was a critical moment"
    },
    {
      "move": "Another key move",
      "significance": "Why this moment mattered"
    }
  ],
  "improvement_tip": "One concrete, actionable study recommendation"
}

IMPORTANT: For strengths and weaknesses, analyze PATTERNS across the whole game (openings, tactics, endgame, positional understanding, piece coordination, pawn structure) — NOT individual good or bad moves.
PROMPT;
    }
}
