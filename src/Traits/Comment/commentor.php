<?php

namespace Miladimos\Social\Traits\Comment;

use Miladimos\Social\Models\Comment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Commentor
{
    public static function booted()
    {
        static::deleting(function ($model) {
            $model->comments()->delete();
        });
    }

    public function mustBeCommentApprove(): bool
    {
        return config('social.comments.need_approve') ?? true;
    }

    /**
     * @return string
     */
    public function commentableModel()
    {
        return config('social.comments.model');
    }

    public function comments()
    {
        return $this->commentsRelation();
    }

    public function commentsRelation(): HasMany
    {
        return $this->morphMany($this->commentableModel(), 'commentorable');
    }

    public function approvedComments()
    {
        return $this->morphMany($this->commentableModel(), 'commentorable')->where('approved', true);
    }

    public function comment(Commentable $commentable, string $commentText = '', int $rate = 0): Comment
    {
        $commentModel = $this->commentableModel();

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


    //     /**
    //  * @param $data
    //  * @param Model      $creator
    //  * @param Model|null $parent
    //  *
    //  * @return static
    //  */
    // public function comment($data, Model $creator, Model $parent = null)
    // {
    //     $commentableModel = $this->commentableModel();

    //     $comment = (new $commentableModel())->createComment($this, $data, $creator);

    //     if (!empty($parent)) {
    //         $parent->appendNode($comment);
    //     }

    //     return $comment;
    // }

    // public function comment(string $comment, $guard = 'web')
    // {
    //     return $this->commentAsUser(auth($guard)->user(), $comment);
    // }

    // public function commentAsUser(?Model $user, string $comment)
    // {
    //     $commentClass = $this->commentableModel();

    //     $comment = new $commentClass([
    //         'comment' => $comment,
    //         'approved' => ($user instanceof User) ? !$user->mustBeCommentApprove($this) : false,
    //         'commentor_id' => is_null($user) ? null : $user->getKey(),
    //         'commentable_id' => $this->getKey(),
    //         'commentable_type' => get_class(),
    //     ]);

    //     return $this->comments()->save($comment);
    // }

    public function hasComments(Commentable $commentable): bool
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
