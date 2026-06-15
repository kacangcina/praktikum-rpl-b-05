<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['recipe_id', 'step_number', 'title', 'description'])]
class RecipeStep extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
