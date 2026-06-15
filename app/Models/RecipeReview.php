<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['recipe_id', 'user_id', 'rating', 'comment'])]
class RecipeReview extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
