<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('role');
            }
        });

        DB::table('users')
            ->whereNull('username')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function ($user): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $user->name ?: 'user'.$user->id]);
            });

        DB::statement("ALTER TABLE users MODIFY username VARCHAR(100) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY role ENUM('guest', 'user', 'creator', 'admin') NOT NULL DEFAULT 'user'");

        Schema::table('recipes', function (Blueprint $table) {
            if (! Schema::hasColumn('recipes', 'difficulty')) {
                $table->enum('difficulty', ['mudah', 'sedang', 'sulit'])->default('mudah')->after('description');
            }

            if (! Schema::hasColumn('recipes', 'thumbnail')) {
                $table->string('thumbnail', 500)->nullable()->after('estimated_minutes');
            }
        });

        if (Schema::hasColumn('recipes', 'estimated_minutes') && ! Schema::hasColumn('recipes', 'estimated_time')) {
            DB::statement('ALTER TABLE recipes CHANGE estimated_minutes estimated_time SMALLINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('recipes', 'estimated_time') && ! Schema::hasColumn('recipes', 'estimated_minutes')) {
            DB::statement('ALTER TABLE recipes CHANGE estimated_time estimated_minutes SMALLINT UNSIGNED NOT NULL');
        }

        Schema::table('recipes', function (Blueprint $table) {
            if (Schema::hasColumn('recipes', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }

            if (Schema::hasColumn('recipes', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }
        });

        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'creator'");
        }
    }
};
