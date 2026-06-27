<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'is_verified', 'suspended_at', 'suspension_reason', 'closed_at', 'closure_reason', 'avatar', 'bio'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified' => 'boolean',
            'suspended_at' => 'datetime',
            'closed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function creatorVerifications()
    {
        return $this->hasMany(CreatorVerification::class);
    }

    public function latestCreatorVerification()
    {
        return $this->hasOne(CreatorVerification::class)->latestOfMany();
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function recipeReviews()
    {
        return $this->hasMany(RecipeReview::class);
    }

    public function canPublishRecipes(): bool
    {
        return $this->role === 'user'
            || ($this->role === 'creator' && $this->is_verified);
    }

    public function canUploadVideos(): bool
    {
        return $this->role === 'creator' && $this->is_verified;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'creator' => $this->is_verified ? 'Creator' : 'User',
            default => 'User',
        };
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar
            ? route('media.public', ['path' => $this->avatar])
            : null;
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name ?: $this->username))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
