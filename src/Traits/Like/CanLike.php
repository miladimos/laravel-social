<?php

namespace Miladimos\Social\Traits\Like;

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

    public function likeModel(): string
    {
        return config('social.likes.model');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany($this->likeModel(), 'likerable');
    }

    public function like(Model $object)
    {
        if (! $this->hasLiked($object)) {
            $attributes = [
                'likeable_type' => $object->getMorphClass(),
                'likeable_id' => $object->getKey(),
            ];

            $this->likes()->create($attributes);
        }
    }

    public function unlike(Model $object): bool
    {
        $relation = \app($this->likeModel())
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
            ->exists();
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }
}
