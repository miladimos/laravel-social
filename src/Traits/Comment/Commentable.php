<?php

namespace Miladimos\Social\Traits\Comment;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

     /**
     * @return \App\Models\Comment[]
     */
    public function comments()
    {
        return $this->commentsRelation();
    }

    public function commentsRelation(): MorphMany
    {
        return $this->morphMany(config('social.comments.model'), 'commentable');
    }

    public function approvedComments()
    {
        return $this->morphMany(Config::get('social.comments.model'), 'commentable')->where('approved', true);
    }

    public function comment(string $comment, $guard = 'web')
    {
        return $this->commentAsUser(auth($guard)->user(), $comment);
    }

    public function commentAsUser(?Model $user, string $comment)
    {
        $commentClass = config('social.comments.model');

        $comment = new $commentClass([
            'comment' => $comment,
            'approved' => ($user instanceof User) ? !$user->needsCommentApproval($this) : false,
            'commentor_id' => is_null($user) ? null : $user->getKey(),
            'commentable_id' => $this->getKey(),
            'commentable_type' => get_class(),
        ]);

        return $this->comments()->save($comment);
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

     /**
     * @return string
     */
    public function commentableModel(): string
    {
        return config('laravel-commentable.model');
    }

    /**
     * @return mixed
     */
    public function comments(): MorphMany
    {
        return $this->morphMany($this->commentableModel(), 'commentable');
    }

    /**
     * @param $data
     * @param Model      $creator
     * @param Model|null $parent
     *
     * @return static
     */
    public function comment($data, Model $creator, Model $parent = null)
    {
        $commentableModel = $this->commentableModel();

        $comment = (new $commentableModel())->createComment($this, $data, $creator);

        if (!empty($parent)) {
            $parent->appendNode($comment);
        }

        return $comment;
    }

    /**
     * @param $id
     * @param $data
     * @param Model|null $parent
     *
     * @return mixed
     */
    public function updateComment($id, $data, Model $parent = null)
    {
        $commentableModel = $this->commentableModel();

        $comment = (new $commentableModel())->updateComment($id, $data);

        if (!empty($parent)) {
            $parent->appendNode($comment);
        }

        return $comment;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function deleteComment($id): bool
    {
        $commentableModel = $this->commentableModel();

        return (bool) (new $commentableModel())->deleteComment($id);
    }

    /**
     * @return mixed
     */
    public function commentCount(): int
    {
        return $this->comments->count();
    }
}
