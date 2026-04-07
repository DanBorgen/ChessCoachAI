<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzePgnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pgn' => ['required', 'string', 'min:5', 'max:20000'],
            'player' => ['required', 'string', 'in:White,Black'], // Validate player as 'White' or 'Black'
        ];
    }

    public function messages(): array
    {
        return [
            'pgn.required' => 'Please provide a PGN to analyze.',
            'pgn.min'      => 'That PGN looks too short — paste the full game.',
        ];
    }
}
