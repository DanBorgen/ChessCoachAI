<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyzePgnRequest;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChessAnalysisController extends Controller
{
    public function __construct(
        private readonly GeminiService $gemini
    ) {}

    /**
     * Render the SPA shell. Inertia will mount the Vue component.
     */
    public function index(): Response
    {
        return Inertia::render('ChessCoach');
    }

    /**
     * Receive a PGN string, run it through Gemini (with Redis caching),
     * and return structured JSON analysis.
     */
    public function analyze(AnalyzePgnRequest $request): JsonResponse
    {
        $all = ($request->all()); // Debug the incoming request data

        $pgn = $request->validated('pgn');
        $player = $request->validated('player');

        // Cache key is a hash of the PGN so identical games return instantly
        $cacheKey = 'analysis:' . md5(trim($pgn) . trim($player));

        $analysis = cache()->remember($cacheKey, now()->addHours(24), function () use ($pgn, $player) {
            return $this->gemini->analyzeGame($pgn, $player);
        });

        return response()->json($analysis);
    }
}
