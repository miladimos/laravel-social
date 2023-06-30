<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.likes.table', 'likes');
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function liker()
    {
        return $this->user();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('like.user_foreign_key'));
    }

    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }
}
