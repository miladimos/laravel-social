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

    public function comments(): MorphMany
    {
        return $this->morphMany(config('comment.model'), 'commentable');
    }

    public function canBeRated(): bool
    {
        return false;
    }

    public function mustBeApproved(): bool
    {
        return false;
    }

    public function primaryId(): string
    {
        return (string)$this->getAttribute($this->primaryKey);
    }

    public function averageRate(int $round = 2): float
    {
        if (!$this->canBeRated()) {
            return 0;
        }

        /** @var Builder $rates */
        $rates = $this->comments()->approvedComments();

        if (!$rates->exists()) {
            return 0;
        }

        return round((float)$rates->avg('rate'), $round);
    }

    public function totalCommentsCount(): int
    {
        if (!$this->mustBeApproved()) {
            return $this->comments()->count();
        }

        return $this->comments()->approvedComments()->count();
    }
}
