<?php

namespace Miladimos\Social\Traits\Like;

use Miladimos\Social\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLike
{
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

    public function likes(): HasMany
    {
        return $this->hasMany(config('social.likes.model'), config('social.likes.user_foreign_key'), $this->getKeyName());
    }

    // protected static function bootHasLikes()
    // {
    //     static::deleting(function ($model) {
    //         $model->likes()->delete();
    //     });
    // }

    // public function likedBy(User $user)
    // {
    //     $this->likes()->create(['user_id' => $user->id()]);
    // }

    // public function dislikedBy(User $user)
    // {
    //     optional($this->likes()->where('user_id', $user->id())->first())->delete();
    // }

    // public function likes(): MorphMany
    // {
    //     return $this->morphMany(Like::class, 'likeable');
    // }

    // public function isLikedBy(User $user): bool
    // {
    //     return $this->likes()->where('user_id', $user->id())->exists();
    // }

    // public function likesCount(): int
    // {
    //     return $this->likes()->count();
    // }
}

// trait Favoriteable
// {
//     /**
//      * @return bool
//      */
//     public function isFavoritedBy(Model $user)
//     {
//         if (\is_a($user, config('auth.providers.users.model'))) {
//             if ($this->relationLoaded('favoriters')) {
//                 return $this->favoriters->contains($user);
//             }

//             return ($this->relationLoaded('favorites') ? $this->favorites : $this->favorites())
//                     ->where(\config('favorite.user_foreign_key'), $user->getKey())->count() > 0;
//         }

//         return false;
//     }

//     /**
//      * @return \Illuminate\Database\Eloquent\Relations\MorphMany
//      */
//     public function favorites()
//     {
//         return $this->morphMany(config('favorite.favorite_model'), 'favoriteable');
//     }

//     /**
//      * Return followers.
//      *
//      * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
//      */
//     public function favoriters()
//     {
//         return $this->belongsToMany(
//             config('auth.providers.users.model'),
//             config('favorite.favorites_table'),
//             'favoriteable_id',
//             config('favorite.user_foreign_key')
//         )
//             ->where('favoriteable_type', $this->getMorphClass());
//     }
// }
