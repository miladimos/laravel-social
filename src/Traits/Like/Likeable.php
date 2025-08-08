<?php

namespace Miladimos\Social\Traits\Like;

use App\Models\User;
use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Likeable
{
    protected static function bootLikeable()
    {
        static::deleting(function ($model) {
            $model->likes()->delete();
        });
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    public function likedBy(User $user)
    {
        $this->likes()->create(
            [
                'likeable_id' => $user->id,
                'likeable_type' => get_class($user),
            ]
        );
    }

    public function dislikedBy(User $user)
    {
        optional($this->likes()->where('user_id', $user->id())->first())->delete();
    }

    public function isLikedBy(Model $user): bool
    {
        if (\is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }

            return $this->likers()->where(\config('social.likes.user_foreign_key'), $user->getKey())->exists();
            // return $this->likes()->where('user_id', $user->id())->exists();

        }

        return false;
    }

    public function scopeLikedBy($query, $userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });
    }

    public function liked($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return (bool) $this->likes()
            ->where('user_id', '=', $userId)
            ->count();
    }

    public function getLikedAttribute()
    {
        return $this->liked();
    }

    public function scopeWhereLikedBy($query, $userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });
    }
}
