<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follows extends Model
{
    protected $table = 'social_follows';

    protected $guarded = [];

    protected $dates = ['accepted_at'];

    // follower
    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    // following
    public function followingable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }

    public function scopeWhereFollowableType(Builder $query, $type)
    {
        if (class_exists($type)) {
            $type = new $type();
        }

        if (is_a($type, Model::class)) {
            $type = $type->getMorphClass();
        }

        return $query->where('followable_type', $type);
    }

    public function scopeWhereFollowingableType(Builder $query, $type)
    {
        if (class_exists($type)) {
            $type = new $type();
        }

        if (is_a($type, Model::class)) {
            $type = $type->getMorphClass();
        }

        return $query->where('followingable_type', $type);
    }
}
