<?php

namespace Miladimos\Social\Traits\Like;

use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;

trait Likeable
{
    protected static function bootLikeable()
    {
        static::deleting(function ($model) {
            $model->likes()->delete();
            // $model->removeLikes();
        });
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

    public function like($userId = null)
    {
        $this->incrementLikeCount();
    }

    public function unlike($userId = null)
    {
        $this->decrementLikeCount();
    }

    /**
     * Populate the $model->likes attribute
     */
    public function getLikeCountAttribute()
    {
        return $this->likeCounter ? $this->likeCounter->count : 0;
    }


    public function removeLikes()
    {
        LikeCounter::where('likeable_type', $this->morphClass)->where('likeable_id', $this->id)->delete();
        // $this->likeCounter()->delete();
    }
}
