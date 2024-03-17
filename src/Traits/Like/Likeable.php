<?php

namespace Miladimos\Social\Traits\Like;

use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Models\LikeCounter;

trait Likeable
{
    protected static function bootLikeable()
    {
        static::deleting(function ($model) {
            $model->likes()->delete();
            // $model->removeLikes();
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
        $this->likes()->create(['likeable_id' => $user->id, 'likeable_type' => get_class($user)]);
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

    public function removeLikes()
    {
        $this->likes()->delete();
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


    /**
     * Has the currently logged in user already "liked" the current object
     *
     * @param string $userId
     * @return boolean
     */
    public function liked($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return (bool) $this->likes()
            ->where('user_id', '=', $userId)
            ->count();
    }

    /**
     * Private. Increment the total like count stored in the counter
     */


    /**
     * Did the currently logged in user like this model
     * Example : if($book->liked) { }
     * @return boolean
     */
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
