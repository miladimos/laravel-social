<?php

namespace Miladimos\Social\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Miladimos\Social\Traits\HasUUID;

class Like extends Model
{
    use HasUUID;

    protected $table;

    protected $guarded = [];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.likes.table', 'likes');
    }

    public function likeable(): \Illuminate\Database\Eloquent\Relations\MorphTo
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
