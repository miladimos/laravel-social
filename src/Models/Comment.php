<?php

namespace Miladimos\Social\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $table = config('social.comments.table');

    protected $guarded = [];

    protected $with = ['comments', 'commentator'];

    protected $casts = [
        'approved' => 'boolean'
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function commentator()
    {
        return $this->belongsTo($this->getAuthModelName(), 'commentor_id');
    }

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

    public function parent()
    {
        return $this->belongsTo(Config::get('social.comments.model'), 'parent_id');
    }


    public function scopeApproved($query, $approved)
    {
        return $query->where('approved', $approved);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = str_replace(PHP_EOL, "<br>", $value);
    }


    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getChildren($columns = ['*'])
    {
        return $this->children()->get($columns);
    }

    public function scopeBeforeId($query, $beforeId)
    {
        return $query->where('id', '<', $beforeId);
    }
}
