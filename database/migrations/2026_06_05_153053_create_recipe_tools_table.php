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
        Schema::create('recipe_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('tool_name', 100);
            $table->timestamps();
        });

        if (Schema::hasColumn('recipes', 'tools')) {
            DB::table('recipes')->get(['id', 'tools'])->each(function ($recipe): void {
                foreach (json_decode($recipe->tools ?: '[]', true) ?: [] as $tool) {
                    DB::table('recipe_tools')->insert([
                        'recipe_id' => $recipe->id,
                        'tool_name' => $tool,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            Schema::table('recipes', function (Blueprint $table) {
                $table->dropColumn('tools');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            if (! Schema::hasColumn('recipes', 'tools')) {
                $table->json('tools')->nullable();
            }
        });

        Schema::dropIfExists('recipe_tools');
    }
};
