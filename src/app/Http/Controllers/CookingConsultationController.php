<?php

namespace App\Http\Controllers;

use App\Services\CookingConsultationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CookingConsultationController extends Controller
{
    public function store(
        Request $request,
        CookingConsultationService $consultation,
    ): JsonResponse {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.min' => 'Pertanyaan minimal 5 karakter.',
            'question.max' => 'Pertanyaan maksimal 1000 karakter.',
        ]);

        try {
            return response()->json($consultation->answer($validated['question']));
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }
}
