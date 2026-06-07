<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'title', 'description', 'difficulty', 'estimated_time', 'thumbnail', 'published_at'])]
class Recipe extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tools()
    {
        return $this->hasMany(RecipeTool::class);
    }

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function steps()
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_items')
            ->withPivot('saved_at')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(RecipeReview::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        return Str::startsWith($this->thumbnail, ['http://', 'https://'])
            ? $this->thumbnail
            : Storage::url($this->thumbnail);
    }
}
