<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('ingredient_name', 150);
            $table->string('quantity', 100);
            $table->timestamps();
        });

        if (Schema::hasColumn('recipes', 'ingredients')) {
            DB::table('recipes')->get(['id', 'ingredients'])->each(function ($recipe): void {
                foreach (json_decode($recipe->ingredients ?: '[]', true) ?: [] as $ingredient) {
                    DB::table('recipe_ingredients')->insert([
                        'recipe_id' => $recipe->id,
                        'ingredient_name' => $ingredient,
                        'quantity' => '-',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            Schema::table('recipes', function (Blueprint $table) {
                $table->dropColumn('ingredients');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            if (! Schema::hasColumn('recipes', 'ingredients')) {
                $table->json('ingredients')->nullable();
            }
        });

        Schema::dropIfExists('recipe_ingredients');
    }
};
