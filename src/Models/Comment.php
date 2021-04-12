<?php

namespace Miladimos\Social\Models;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $table = 'comments';

    protected $guarded = [];

    protected $with = ['comments', 'commentator'];

    protected $casts = [
        'approved' => 'boolean'
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The user who posted the comment.
     */
    public function commentorable(): MorphTo
    {
        return $this->morphTo();
    }


    // public function commentator()
    // {
    //     return $this->belongsTo($this->getAuthModelName(), 'commentor_id');
    // }

    protected function getAuthModelName()
    {
        if (config('socical.comments.user_model')) {
            return config('socical.comments.user_model');
        }

        if (!is_null(config('auth.providers.users.model'))) {
            return config('auth.providers.users.model');
        }

        throw new Exception('Could not determine the commentator model name.');
    }

    public function children()
    {
        return $this->hasMany(Config::get('social.comments.model'), 'parent_id');
    }

    // public function hasChildren()
    // {
    //     return $this->children()->count() > 0;
    // }

    // public function getChildren($columns = ['*'])
    // {
    //     return $this->children()->get($columns);
    // }

    public function parent()
    {
        return $this->belongsTo(Config::get('social.comments.model'), 'parent_id');
    }

    public function scopeApproved(Builder $query, $approved): Builder
    {
        return $query->where('approved', $approved);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = str_replace(PHP_EOL, "<br>", $value);
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    /**
     * @param Model $commentable
     * @param $data
     * @param Model $creator
     *
     * @return static
     */
    public function createComment(Model $commentable, $data, Model $creator): self
    {
        return $commentable->comments()->create(array_merge($data, [
            'creator_id'   => $creator->getAuthIdentifier(),
            'creator_type' => $creator->getMorphClass(),
        ]));
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateComment($id, $data): bool
    {
        return (bool) static::find($id)->update($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function deleteComment($id): bool
    {
        return (bool) static::find($id)->delete();
    }

    public function approve(): self
    {
        $this->approved = true;
        $this->save();

        return $this;
    }
}
