<?php

namespace Miladimos\Social\Traits\Comment;

use Illuminate\Support\Facades\Config;

trait Commentor
{

    public function comments()
    {
        return $this->morphMany(Config::get('social.comments.model'), 'commentor');
    }


    public function approvedComments()
    {
        return $this->morphMany(Config::get('social.comments.model'), 'commentor')->where('approved', true);
    }


    public function needsCommentApproval($model): bool
    {
        return true;
    }

}
