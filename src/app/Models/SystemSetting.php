<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'updated_by'];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function valueFor(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }
}
