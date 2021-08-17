<?php

namespace Miladimos\Social\Models;

use Miladimos\Social\Traits\HasUUID;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Miladimos\Social\Traits\RouteKeyNameUUID;

class Comment extends Model
{
    use HasUUID,
        RouteKeyNameUUID;

    protected $table = 'comments';

    protected $guarded = [];

    protected $with = ['children', 'commentator', 'commentable'];

    protected $casts = [
        'approved' => 'boolean'
    ];

    /**
     * The user who posted the comment.
     */
    public function commentorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
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
        ]);

        return $this;
    }

    public function disapprove()
    {
        $this->update([
            'is_approved' => false,
        ]);

        return $this;
    }
}
