<?php

namespace Laravelista\Comments;

use Illuminate\Support\Facades\Config;

/**
 * Add this trait to any model that you want to be able to
 * comment upon or get comments for.
 */
trait Commentable
{
    /**
     * This static method does voodoo magic to
     * delete leftover comments once the commentable
     * model is deleted.
     */
    protected static function bootCommentable()
    {
        static::deleted(function($commentable) {
            foreach ($commentable->comments as $comment) {
                $comment->delete();
            }
        });
    }

    /**
     * Returns all comments for this model.
     */
    public function comments()
    {
        return $this->morphMany(Config::get('comments.model'), 'commentable');
    }

    /**
     * Returns only approved comments for this model.
     */
    public function approvedComments()
    {
        return $this->morphMany(Config::get('comments.model'), 'commentable')->where('approved', true);
    }
}


namespace BeyondCode\Comments\Traits;


use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Contracts\Commentator;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /**
     * Return all comments for this model.
     *
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany(config('comments.comment_class'), 'commentable');
    }

    /**
     * Attach a comment to this model.
     *
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function comment(string $comment)
    {
        return $this->commentAsUser(auth()->user(), $comment);
    }

    /**
     * Attach a comment to this model as a specific user.
     *
     * @param Model|null $user
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function commentAsUser(?Model $user, string $comment)
    {
        $commentClass = config('comments.comment_class');

        $comment = new $commentClass([
            'comment' => $comment,
            'is_approved' => ($user instanceof Commentator) ? ! $user->needsCommentApproval($this) : false,
            'user_id' => is_null($user) ? null : $user->getKey(),
            'commentable_id' => $this->getKey(),
            'commentable_type' => get_class(),
        ]);

        return $this->comments()->save($comment);
    }

}
