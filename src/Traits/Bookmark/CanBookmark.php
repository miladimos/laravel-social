<?php

namespace Miladimos\Social\Traits\Like;

trait CanLike
{
/**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Like
     */
    public function like(Model $object): Like
    {
        $attributes = [
            'likeable_type' => $object->getMorphClass(),
            'likeable_id' => $object->getKey(),
            config('like.user_foreign_key') => $this->getKey(),
        ];

        /* @var \Illuminate\Database\Eloquent\Model $like */
        $like = \app(config('like.like_model'));

        /* @var \Overtrue\LaravelLike\Traits\Likeable|\Illuminate\Database\Eloquent\Model $object */
        return $like->where($attributes)->firstOr(
            function () use ($like, $attributes) {
                $like->unguard();

                if ($this->relationLoaded('likes')) {
                    $this->unsetRelation('likes');
                }

                return $like->create($attributes);
            }
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return bool
     * @throws \Exception
     */
    public function unlike(Model $object): bool
    {
        /* @var \Overtrue\LaravelLike\Like $relation */
        $relation = \app(config('like.like_model'))
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->where(config('like.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            if ($this->relationLoaded('likes')) {
                $this->unsetRelation('likes');
            }

            return $relation->delete();
        }

        return true;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Like|null
     * @throws \Exception
     */
    public function toggleLike(Model $object)
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return bool
     */
    public function hasLiked(Model $object): bool
    {
        return ($this->relationLoaded('likes') ? $this->likes : $this->likes())
                ->where('likeable_id', $object->getKey())
                ->where('likeable_type', $object->getMorphClass())
                ->count() > 0;
    }

    public function likes(): HasMany
    {
        return $this->hasMany(config('like.like_model'), config('like.user_foreign_key'), $this->getKeyName());
    }


    trait Favoriteable
{
    /**
     * @return bool
     */
    public function isFavoritedBy(Model $user)
    {
        if (\is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('favoriters')) {
                return $this->favoriters->contains($user);
            }

            return ($this->relationLoaded('favorites') ? $this->favorites : $this->favorites())
                    ->where(\config('favorite.user_foreign_key'), $user->getKey())->count() > 0;
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(config('favorite.favorite_model'), 'favoriteable');
    }

    /**
     * Return followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriters()
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('favorite.favorites_table'),
            'favoriteable_id',
            config('favorite.user_foreign_key')
        )
            ->where('favoriteable_type', $this->getMorphClass());
    }
}
}
