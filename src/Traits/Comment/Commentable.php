<?php

namespace Miladimos\Social\Traits\Comment;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

trait Commentable
{
    protected static function bootCommentable()
    {
        static::deleted(function ($commentable) {
            foreach ($commentable->comments as $comment) {
                $comment->delete();
            }
        });
    }

    public function comments()
    {
        return $this->morphMany(config('social.comments.model'), 'commentable');
    }

    public function approvedComments()
    {
        return $this->morphMany(Config::get('social.comments.model'), 'commentable')->where('approved', true);
    }

    public function comment(string $comment)
    {
        return $this->commentAsUser(auth()->user(), $comment);
    }

    public function commentAsUser(?Model $user, string $comment)
    {
        $commentClass = config('social.comments.model');

        $comment = new $commentClass([
            'comment' => $comment,
            'approved' => ($user instanceof Commentator) ? !$user->needsCommentApproval($this) : false,
            'commentor_id' => is_null($user) ? null : $user->getKey(),
            'commentable_id' => $this->getKey(),
            'commentable_type' => get_class(),
        ]);

        return $this->comments()->save($comment);
    }
}
