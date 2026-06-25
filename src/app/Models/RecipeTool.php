<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['recipe_id', 'tool_name'])]
class RecipeTool extends Model
{
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
