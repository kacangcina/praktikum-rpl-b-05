<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'recipe_reviews',
            'user_follows',
            'collection_items',
            'collections',
            'videos',
            'recipe_tools',
            'recipe_steps',
            'recipe_ingredients',
            'creator_verifications',
            'notifications',
            'recipes',
            'jobs',
            'job_batches',
            'failed_jobs',
            'cache',
            'cache_locks',
            'password_reset_tokens',
            'sessions',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        $columns = collect(['role', 'is_verified', 'avatar', 'bio'])
            ->filter(fn (string $column) => Schema::hasColumn('users', $column))
            ->all();

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        // Removed features are intentionally not recreated.
    }
};
