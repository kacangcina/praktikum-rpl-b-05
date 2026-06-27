<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\SystemSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CookingConsultationService
{
    public const PROMPT_KEY = 'ai_system_prompt';

    public const OUT_OF_SCOPE_MESSAGE = 'Maaf, saya hanya dapat membantu pertanyaan seputar memasak dan resep.';

    public const DEFAULT_SYSTEM_PROMPT = <<<'PROMPT'
Kamu adalah asisten konsultasi memasak CuBu.
Jawab hanya pertanyaan tentang memasak, resep, bahan, alat dapur, teknik memasak, keamanan pangan, atau memperbaiki hasil masakan.
Prioritaskan informasi dari konteks resep CuBu yang diberikan. Jangan menyatakan suatu resep tersedia di CuBu jika tidak ada di konteks.
Gunakan Bahasa Indonesia yang ringkas, ramah, praktis, dan mudah diikuti.
Jangan mengarang diagnosis medis. Untuk risiko kesehatan atau keracunan serius, sarankan mencari bantuan profesional.
PROMPT;

    private const SCOPE_INSTRUCTION = <<<'PROMPT'
Aturan klasifikasi cakupan yang wajib:
- Set in_scope ke true untuk semua pertanyaan tentang memasak, memanggang, menggoreng, merebus, resep, bahan makanan, adonan, alat dapur, teknik memasak, keamanan pangan, atau memperbaiki hasil masakan.
- Pertanyaan singkat tetap termasuk cakupan apabila menyebut makanan atau masalah memasak. Contoh yang harus in_scope: "cara agar telur tidak gosong", "mengapa donat tidak mengembang", "nasi terlalu lembek", dan "ayam masih mentah di dalam".
- Tidak ditemukannya resep yang cocok dalam konteks CuBu bukan alasan untuk menolak pertanyaan. Tetap berikan saran memasak umum dan kosongkan related_recipe_ids.
- Set in_scope ke false hanya jika pertanyaan jelas tidak berkaitan dengan makanan, dapur, memasak, atau resep.
PROMPT;

    public function answer(string $question): array
    {
        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            throw new RuntimeException('Layanan konsultasi AI belum dikonfigurasi.');
        }

        $recipes = $this->relevantRecipes($question);
        $model = config('services.gemini.model');
        $url = rtrim(config('services.gemini.base_url'), '/')."/models/{$model}:generateContent";

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->acceptJson()
                ->timeout(30)
                ->retry(2, 300)
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [[
                            'text' => $this->systemPrompt(),
                        ]],
                    ],
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [[
                            'text' => $this->userPrompt($question, $recipes),
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 600,
                        'responseMimeType' => 'application/json',
                        'responseJsonSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'in_scope' => ['type' => 'boolean'],
                                'answer' => ['type' => 'string'],
                                'related_recipe_ids' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'integer'],
                                ],
                            ],
                            'required' => ['in_scope', 'answer', 'related_recipe_ids'],
                            'additionalProperties' => false,
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Layanan konsultasi AI sedang tidak dapat dihubungi.', previous: $exception);
        } catch (RequestException $exception) {
            report(new RuntimeException('Gemini API error: '.$exception->response?->body(), previous: $exception));
            throw new RuntimeException('Layanan konsultasi AI sedang sibuk. Coba lagi beberapa saat lagi.', previous: $exception);
        }

        if ($response->failed()) {
            report(new RuntimeException('Gemini API error: '.$response->body()));
            throw new RuntimeException('Layanan konsultasi AI sedang tidak tersedia.');
        }

        $result = json_decode($this->responseText($response->json()), true);

        if (! is_array($result) || ! array_key_exists('in_scope', $result)) {
            throw new RuntimeException('Respons layanan konsultasi AI tidak valid.');
        }

        $inScope = (bool) $result['in_scope'];
        $answer = trim((string) ($result['answer'] ?? ''));

        if ($inScope && $answer === '') {
            throw new RuntimeException('Respons layanan konsultasi AI tidak memiliki jawaban.');
        }

        $relatedIds = collect($result['related_recipe_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique();

        return [
            'in_scope' => $inScope,
            'answer' => $inScope ? $answer : self::OUT_OF_SCOPE_MESSAGE,
            'related_recipes' => $inScope
                ? $recipes->whereIn('id', $relatedIds)->map(fn (Recipe $recipe) => [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'thumbnail_url' => $recipe->thumbnail_url,
                ])->values()
                : [],
        ];
    }

    private function relevantRecipes(string $question): Collection
    {
        $keywords = collect(preg_split('/\s+/u', Str::lower($question)))
            ->map(fn (string $word) => trim($word, ".,!?;:()[]{}\"'"))
            ->filter(fn (string $word) => mb_strlen($word) >= 4)
            ->unique()
            ->take(6)
            ->values();

        $query = Recipe::query()
            ->published()
            ->with(['ingredients', 'steps', 'tools'])
            ->latest('published_at');

        if ($keywords->isNotEmpty()) {
            $query->where(function ($query) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $like = '%'.$keyword.'%';
                    $query->orWhere('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('ingredients', fn ($ingredients) => $ingredients
                            ->where('ingredient_name', 'like', $like));
                }
            });
        }

        $recipes = $query->limit(5)->get();

        if ($recipes->isEmpty()) {
            return Recipe::query()
                ->published()
                ->with(['ingredients', 'steps', 'tools'])
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        return $recipes;
    }

    private function userPrompt(string $question, Collection $recipes): string
    {
        $context = $recipes->map(function (Recipe $recipe): string {
            $ingredients = $recipe->ingredients
                ->map(fn ($ingredient) => trim($ingredient->quantity.' '.$ingredient->ingredient_name))
                ->implode(', ');
            $steps = $recipe->steps
                ->map(fn ($step) => $step->step_number.'. '.$step->description)
                ->implode(' ');

            return implode("\n", [
                "ID: {$recipe->id}",
                "Judul: {$recipe->title}",
                "Deskripsi: {$recipe->description}",
                "Bahan: {$ingredients}",
                "Langkah: {$steps}",
            ]);
        })->implode("\n\n");

        return "Konteks resep CuBu:\n".($context ?: 'Tidak ada resep yang relevan.').
            "\n\nPertanyaan pengguna:\n{$question}";
    }

    private function systemPrompt(): string
    {
        $adminPrompt = trim((string) SystemSetting::valueFor(
            self::PROMPT_KEY,
            self::DEFAULT_SYSTEM_PROMPT,
        ));

        return $adminPrompt."\n\n".self::SCOPE_INSTRUCTION;
    }

    private function responseText(array $payload): string
    {
        $text = data_get($payload, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Respons layanan konsultasi AI tidak memiliki teks.');
        }

        return $text;
    }
}
