<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_verifications', function (Blueprint $table): void {
            $table->index(
                ['status', 'submitted_at', 'id'],
                'creator_verifications_admin_list_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('creator_verifications', function (Blueprint $table): void {
            $table->dropIndex('creator_verifications_admin_list_index');
        });
    }
};
