<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $table = 'social_likes';

    protected $guarded = [];

    // liked model
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    // liker
    public function likerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }
}
