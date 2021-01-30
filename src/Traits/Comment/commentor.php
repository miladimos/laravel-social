<?php

namespace Miladimos\Social\Traits\Comment;

use Illuminate\Support\Facades\Config;

trait Commenter
{
    /**
     * Returns all comments that this user has made.
     */
    public function comments()
    {
        return $this->morphMany(Config::get('comments.model'), 'commenter');
    }

    /**
     * Returns only approved comments that this user has made.
     */
    public function approvedComments()
    {
        return $this->morphMany(Config::get('comments.model'), 'commenter')->where('approved', true);
    }

    /**
     * Check if a comment for a specific model needs to be approved.
     * @param mixed $model
     * @return bool
     */
    public function needsCommentApproval($model): bool
    {
        return true;
    }
}
