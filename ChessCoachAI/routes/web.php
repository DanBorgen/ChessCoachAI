<?php

use App\Http\Controllers\ChessAnalysisController;
use Illuminate\Support\Facades\Route;

// Serve the Vue SPA shell via Inertia
Route::get('/', [ChessAnalysisController::class, 'index'])->name('home');

// API endpoint — receives PGN, returns analysis JSON
Route::post('/analyze', [ChessAnalysisController::class, 'analyze'])->name('analyze');
