<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CookingConsultationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CookingConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'test-key',
            'services.gemini.base_url' => 'https://generativelanguage.test/v1beta',
            'services.gemini.model' => 'gemini-test',
        ]);
    }

    public function test_authenticated_user_receives_advice_and_related_recipe(): void
    {
        $recipe = $this->createRecipe();
        Http::fake([
            '*' => Http::response($this->responsePayload([
                'in_scope' => true,
                'answer' => 'Gunakan api kecil agar ayam matang merata.',
                'related_recipe_ids' => [$recipe->id],
            ])),
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson('/api/cooking-consultation', [
                'question' => 'Bagaimana memasak ayam agar matang merata?',
            ])
            ->assertOk()
            ->assertJsonPath('in_scope', true)
            ->assertJsonPath('answer', 'Gunakan api kecil agar ayam matang merata.')
            ->assertJsonPath('related_recipes.0.id', $recipe->id);

        Http::assertSent(function ($request) use ($recipe): bool {
            return $request->url() === 'https://generativelanguage.test/v1beta/models/gemini-test:generateContent'
                && $request->hasHeader('x-goog-api-key', 'test-key')
                && $request['generationConfig']['responseMimeType'] === 'application/json'
                && str_contains($request['contents'][0]['parts'][0]['text'], $recipe->title);
        });
    }

    public function test_dynamic_admin_prompt_is_used_as_system_instruction(): void
    {
        SystemSetting::create([
            'key' => CookingConsultationService::PROMPT_KEY,
            'value' => 'Jawab sebagai chef khusus masakan Nusantara dan selalu ringkas.',
        ]);
        Http::fake(['*' => Http::response($this->responsePayload([
            'in_scope' => true,
            'answer' => 'Gunakan santan segar.',
            'related_recipe_ids' => [],
        ]))]);

        $this->actingAs(User::factory()->create())
            ->postJson('/api/cooking-consultation', ['question' => 'Bagaimana memilih santan?'])
            ->assertOk();

        Http::assertSent(function ($request): bool {
            $prompt = $request['system_instruction']['parts'][0]['text'];

            return str_starts_with(
                $prompt,
                'Jawab sebagai chef khusus masakan Nusantara dan selalu ringkas.',
            )
                && str_contains($prompt, 'cara agar telur tidak gosong')
                && str_contains($prompt, 'mengapa donat tidak mengembang')
                && str_contains($prompt, 'bukan alasan untuk menolak pertanyaan');
        });
    }

    public function test_out_of_scope_question_uses_the_documented_rejection_message(): void
    {
        Http::fake(['*' => Http::response($this->responsePayload([
            'in_scope' => false,
            'answer' => '',
            'related_recipe_ids' => [],
        ]))]);

        $this->actingAs(User::factory()->create())
            ->postJson('/api/cooking-consultation', [
                'question' => 'Siapa presiden negara lain saat ini?',
            ])
            ->assertOk()
            ->assertJson([
                'in_scope' => false,
                'answer' => CookingConsultationService::OUT_OF_SCOPE_MESSAGE,
                'related_recipes' => [],
            ]);
    }

    public function test_guest_cannot_use_cooking_consultation(): void
    {
        $this->postJson('/api/cooking-consultation', [
            'question' => 'Mengapa nasi saya terlalu lembek?',
        ])->assertUnauthorized();
    }

    public function test_question_is_required_and_limited(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/cooking-consultation', ['question' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('question');

        $this->actingAs($user)
            ->postJson('/api/cooking-consultation', ['question' => str_repeat('a', 1001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('question');

        Http::assertNothingSent();
    }

    public function test_missing_api_key_returns_service_unavailable(): void
    {
        config(['services.gemini.api_key' => null]);

        $this->actingAs(User::factory()->create())
            ->postJson('/api/cooking-consultation', [
                'question' => 'Mengapa kue saya bantat?',
            ])
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'Layanan konsultasi AI belum dikonfigurasi.');
    }

    private function createRecipe(): Recipe
    {
        $recipe = Recipe::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Ayam Panggang Merata',
            'description' => 'Ayam matang merata dengan suhu rendah.',
            'difficulty' => 'mudah',
            'estimated_time' => 60,
            'published_at' => now(),
        ]);
        $recipe->ingredients()->create(['ingredient_name' => 'Ayam', 'quantity' => '1 ekor']);
        $recipe->steps()->create(['step_number' => 1, 'description' => 'Panggang dengan api kecil.']);

        return $recipe;
    }

    private function responsePayload(array $result): array
    {
        return [
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => json_encode($result, JSON_THROW_ON_ERROR),
                    ]],
                ],
            ]],
        ];
    }
}
