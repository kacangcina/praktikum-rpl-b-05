<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\CookingConsultationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $setting = SystemSetting::query()
            ->with('updatedBy:id,name,username')
            ->where('key', CookingConsultationService::PROMPT_KEY)
            ->first();

        return response()->json([
            'prompt' => $setting?->value ?? CookingConsultationService::DEFAULT_SYSTEM_PROMPT,
            'model' => config('services.gemini.model'),
            'updated_at' => $setting?->updated_at,
            'updated_by' => $setting?->updatedBy,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'prompt' => ['required', 'string', 'min:20', 'max:10000'],
        ]);

        $setting = SystemSetting::query()->updateOrCreate(
            ['key' => CookingConsultationService::PROMPT_KEY],
            ['value' => trim($validated['prompt']), 'updated_by' => $request->user()->id],
        );

        return response()->json([
            'message' => 'System prompt berhasil disimpan.',
            'prompt' => $setting->value,
            'updated_at' => $setting->updated_at,
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
