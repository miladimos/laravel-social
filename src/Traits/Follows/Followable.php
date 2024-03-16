<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Model;

trait Followable
{
    public static function bootFollowable()
    {
        static::deleted(function (Model $entity) {
            $entity->followers()->delete();
        });
    }

    public function followableMorphs(): string
    {
        return config('social.follows.followable_morphs');
    }

    /**
     * Returns the followers that this entity is associated with.
     */
    public function followers():MorphMany
    {
        return $this->morphMany(config('social.follows.model'), $this->followableMorphs())->withPivot('accepted_at')->withTimestamps();
    }
}
