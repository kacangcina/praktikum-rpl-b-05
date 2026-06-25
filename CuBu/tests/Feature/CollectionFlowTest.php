<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_remove_recipe_from_personal_collection(): void
    {
        $user = User::factory()->create();
        $recipe = $this->createRecipe();

        $this->actingAs($user)
            ->post(route('collections.store', $recipe))
            ->assertSessionHas('status', 'Resep berhasil disimpan ke Koleksi Saya.');

        $this->assertDatabaseHas('collection_items', ['recipe_id' => $recipe->id]);

        $this->actingAs($user)
            ->getJson('/api/collection')
            ->assertOk()
            ->assertJsonFragment([
                'title' => $recipe->title,
                'is_saved' => true,
            ]);

        $this->actingAs($user)
            ->delete(route('collections.destroy', $recipe))
            ->assertSessionHas('status', 'Resep dihapus dari koleksi.');

        $this->assertDatabaseMissing('collection_items', ['recipe_id' => $recipe->id]);
    }

    public function test_recipe_cards_report_the_authenticated_users_saved_state(): void
    {
        $user = User::factory()->create();
        $savedRecipe = $this->createRecipe();
        $otherRecipe = $this->createRecipe();
        $otherRecipe->update(['title' => 'Resep Belum Disimpan']);

        $this->actingAs($user)->postJson('/api/collection/'.$savedRecipe->id);

        $response = $this->actingAs($user)->getJson('/api/recipes');

        $response->assertOk();

        $recipes = collect($response->json('recipes'))->keyBy('id');
        $this->assertTrue($recipes[$savedRecipe->id]['is_saved']);
        $this->assertFalse($recipes[$otherRecipe->id]['is_saved']);
    }

    public function test_duplicate_recipe_is_not_added_to_same_collection(): void
    {
        $user = User::factory()->create();
        $recipe = $this->createRecipe();

        $this->actingAs($user)->post(route('collections.store', $recipe));
        $this->actingAs($user)
            ->post(route('collections.store', $recipe))
            ->assertSessionHas('status', 'Resep sudah ada di koleksi ini.');

        $this->assertDatabaseCount('collection_items', 1);
    }

    private function createRecipe(): Recipe
    {
        $owner = User::factory()->create();

        return Recipe::create([
            'user_id' => $owner->id,
            'title' => 'Nasi Goreng Koleksi',
            'description' => 'Resep untuk disimpan.',
            'difficulty' => 'mudah',
            'estimated_time' => 15,
            'published_at' => now(),
        ]);
    }
}
