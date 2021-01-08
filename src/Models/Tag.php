<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
//    protected $table = config('social.likes.table', 'likes');
    protected $table = 'likes';

    protected $fillable = ['user_id', 'likeable_id', 'likeable_type'];

    public $timestamps = true;

    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = \config('like.likes_table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function ($like) {
            $userForeignKey = \config('like.user_foreign_key');
            $like->{$userForeignKey} = $like->{$userForeignKey} ?: auth()->id();


            if (\config('like.uuids')) {
                $like->{$like->getKeyName()} = $like->{$like->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
    }

    public function likeable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\config('auth.providers.users.model'), \config('like.user_foreign_key'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liker()
    {
        return $this->user();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }
}
