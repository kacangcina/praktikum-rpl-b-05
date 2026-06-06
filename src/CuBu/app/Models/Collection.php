<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name'])]
class Collection extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'collection_items')
            ->withPivot('saved_at')
            ->withTimestamps();
    }
}
