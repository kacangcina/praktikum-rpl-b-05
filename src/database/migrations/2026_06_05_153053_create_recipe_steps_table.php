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
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_number');
            $table->text('description');
            $table->timestamps();
        });

        if (Schema::hasColumn('recipes', 'steps')) {
            DB::table('recipes')->get(['id', 'steps'])->each(function ($recipe): void {
                foreach (json_decode($recipe->steps ?: '[]', true) ?: [] as $index => $step) {
                    DB::table('recipe_steps')->insert([
                        'recipe_id' => $recipe->id,
                        'step_number' => $index + 1,
                        'description' => $step,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            Schema::table('recipes', function (Blueprint $table) {
                $table->dropColumn('steps');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            if (! Schema::hasColumn('recipes', 'steps')) {
                $table->json('steps')->nullable();
            }
        });

        Schema::dropIfExists('recipe_steps');
    }
};
