<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->enum('type', ['upvote', 'downvote']);
            $table->timestamp('created_at')->useCurrent();
            
            // Mencegah duplikasi rating pada resep yang sama
            $table->unique(['user_id', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};