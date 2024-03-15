<?php

namespace Miladimos\Social\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Miladimos\Social\Traits\RouteKeyNameUUID;

class Comment extends Model
{
    protected $guarded = [];

    protected $with = ['children', 'commentator', 'commentable'];

    protected $casts = [
        'approved' => 'boolean',
        'approved_at' => 'date'
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('social.comments.table', 'social_comments');

        parent::__construct($attributes);
    }

    /**
     * The user who posted the comment.
     */
    public function commentorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     * model
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getChildren($columns = ['*'])
    {
        return $this->children()->get($columns);
    }

    public function children()
    {
        return $this->hasMany(Config::get('social.comments.model'), 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Config::get('social.comments.model'), 'id', 'parent_id');
    }

    public function scopeApproved(Builder $query, $approved): Builder
    {
        return $query->where('approved', $approved);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = str_replace(PHP_EOL, "<br>", $value);
    }

    public function approve()
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return $this;
    }

    public function disapprove()
    {
        $this->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return $this;
    }


      // public function commentator()
      // {
      //     return $this->belongsTo($this->getAuthModelName(), 'user_id');
      // }
      //
    protected function getAuthModelName()
{
    if (config('comments.user_model')) {
        return config('comments.user_model');
    }

    if (!is_null(config('auth.providers.users.model'))) {
        return config('auth.providers.users.model');
    }

    throw new Exception('Could not determine the commentator model name.');
}
}
