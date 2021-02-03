<?php

trait Likeable
{
    public static function bootLikeable()
    {
        if (static::removeLikesOnDelete()) {
            static::deleting(function ($model) {
                $model->removeLikes();
            });
        }
    }

    public function getLikeCountAttribute()
    {
        return $this->likeCounter ? $this->likeCounter->count : 0;
    }

    public function like($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId) {
            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();

            if ($like) {
                return;
            }

            $like = new Like();
            $like->user_id = $userId;
            $this->likes()->save($like);
        }

        $this->incrementLikeCount();
    }

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

    public function liked($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return (bool) $this->likes()
            ->where('user_id', '=', $userId)
            ->count();
    }

    public static function removeLikesOnDelete()
    {
        return isset(static::$removeLikesOnDelete)
            ? static::$removeLikesOnDelete
            : true;
    }

    public function removeLikes()
    {
        $this->likes()->delete();
        $this->likeCounter()->delete();
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getLikedAttribute()
    {
        return $this->liked();
    }

    public function likeCounter()
    {
        return $this->morphOne(LikeCounter::class, 'likeable');
    }

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

    public function scopeWhereLikedBy($query, $userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });
    }

    private function loggedInUserId()
    {
        return auth()->id();
    }
}
