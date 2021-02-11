<?php

namespace Miladimos\Social\Traits\Comment;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Commentor
{

    public function comments()
    {
        return $this->commentsRelation();
    }

    public function commentsRelation(): HasMany
    {
        return $this->hasMany(config('social.comments.model'));
    }

    public function approvedComments()
    {
        return $this->morphMany(config('social.comments.model'), 'commentor')->where('approved', true);
    }

    public function needsCommentApproval(): bool
    {
        return true;
    }

    public function comment(Commentable $commentable, string $commentText = '', int $rate = 0): Comment
    {
        $commentModel = config('social.comments.model');

        $comment = new $commentModel([
            'comment'        => $commentText,
            'rate'           => $commentable->canBeRated() ? $rate : null,
            'approved'       => $commentable->mustBeApproved() && !$this->canCommentWithoutApprove() ? false : true,
            'commented_id'   => $this->primaryId(),
            'commented_type' => get_class(),
        ]);

        $commentable->comments()->save($comment);

        return $comment;
    }

    public function canCommentWithoutApprove(): bool
    {
        return false;
    }

    public function hasCommentsOn(Commentable $commentable): bool
    {
        return $this->comments()
            ->where([
                'commentable_id'   => $commentable->primaryId(),
                'commentable_type' => get_class($commentable),
            ])
            ->exists();
    }

    private function primaryId(): string
    {
        return (string)$this->getAttribute($this->primaryKey);
    }
}
