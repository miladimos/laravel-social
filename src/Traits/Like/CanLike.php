<?php

namespace Miladimos\Social\Traits\Like;

use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CanLike
{
    protected static function bootCanLike()
    {
        static::deleting(function ($model) {
            $model->likes()->delete();
        });
    }

    public function likes(): MorphMany
    {
// favoriteable
        return $this->morphMany(config('social.likes.model'), config('social.likes.user_foreign_key'), $this->getKeyName());
    }

    public function like(Model $object): Like
    {
        $attributes = [
            'likeable_type' => $object->getMorphClass(),
            'likeable_id' => $object->getKey(),
            config('social.likes.user_foreign_key') => $this->getKey(),
        ];

        $like = \app(config('social.likes.model'));

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

    public function unlike(Model $object): bool
    {
        $relation = \app(config('social.likes.model'))
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->where(config('social.likes.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            if ($this->relationLoaded('likes')) {
                $this->unsetRelation('likes');
            }

            return $relation->delete();
        }

        return true;
    }

    public function toggleLike(Model $object)
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

    public function hasLiked(Model $object): bool
    {
        return ($this->relationLoaded('likes') ? $this->likes : $this->likes())
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }
}
