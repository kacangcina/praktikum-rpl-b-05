<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'recipe_id', 'title', 'description', 'difficulty', 'file_path'])]
class Video extends Model
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getFileUrlAttribute(): string
    {
        return route('recipes.video.watch', $this->recipe_id);
    }
}
