<?php

namespace Miladimos\Social\Traits\Like;

use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Models\LikeCounter;

trait Likeable
{

    public static function booted()
    {
        if (static::removeLikesOnDelete()) {
            static::deleting(function ($model) {
                $model->removeLikes();
            });
        }
    }


    /**
     * Fetch the primary ID of the currently logged in user
     * @return number
     */
    public function loggedInUserId()
    {
        return auth()->id();
    }


    public function isLikedBy(Model $user): bool
    {
        if (\is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }

            return $this->likers()->where(\config('social.likes.user_foreign_key'), $user->getKey())->exists();
        }

        return false;
    }

    public function likers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('social.likes.likes_table'),
            'likeable_id',
            config('social.likes.user_foreign_key')
        )
            ->where('likeable_type', $this->getMorphClass());
    }

    /**
     * Fetch records that are liked by a given user.
     * Ex: Post::likedBy(123)->get();
     */
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
     * Populate the $model->likes attribute
     */
    public function getLikeCountAttribute()
    {
        return $this->likeCounter ? $this->likeCounter->count : 0;
    }

    /**
     * Collection of the likes on this record
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Counter is a record that stores the total likes for the
     * morphed record
     */
    public function likeCounter()
    {
        return $this->morphOne(LikeCounter::class, 'likeable');
    }

    /**
     * Add a like for this record by the given user.
     * @param $userId mixed - If null will use currently logged in user.
     */
    public function like($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId) {
            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();

            if ($like) return;

            $like = new Like();
            $like->user_id = $userId;
            $this->likes()->save($like);
        }

        $this->incrementLikeCount();
    }

    /**
     * Remove a like from this record for the given user.
     * @param $userId mixed - If null will use currently logged in user.
     */
    public function unlike($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId) {
            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();

            if (!$like) {
                return;
            }

            $like->delete();
        }

        $this->decrementLikeCount();
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
    private function incrementLikeCount()
    {
        $counter = $this->likeCounter()->first();

        if ($counter) {
            $counter->count++;
            $counter->save();
        } else {
            $counter = new LikeCounter;
            $counter->count = 1;
            $this->likeCounter()->save($counter);
        }
    }

    /**
     * Private. Decrement the total like count stored in the counter
     */
    private function decrementLikeCount()
    {
        $counter = $this->likeCounter()->first();

        if ($counter) {
            $counter->count--;
            if ($counter->count) {
                $counter->save();
            } else {
                $counter->delete();
            }
        }
    }

    /**
     * Did the currently logged in user like this model
     * Example : if($book->liked) { }
     * @return boolean
     */
    public function getLikedAttribute()
    {
        return $this->liked();
    }

    /**
     * Should remove likes on model row delete (defaults to true)
     * public static removeLikesOnDelete = false;
     */
    public static function removeLikesOnDelete()
    {
        return isset(static::$removeLikesOnDelete)
            ? static::$removeLikesOnDelete
            : true;
    }

    /**
     * Delete likes related to the current record
     */
    public function removeLikes()
    {
        Like::where('likeable_type', $this->morphClass)->where('likeable_id', $this->id)->delete();

        LikeCounter::where('likeable_type', $this->morphClass)->where('likeable_id', $this->id)->delete();

        // $this->likes()->delete();
        // $this->likeCounter()->delete();
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
