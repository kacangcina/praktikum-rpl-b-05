<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['recipe_id', 'ingredient_name', 'quantity'])]
class RecipeIngredient extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
