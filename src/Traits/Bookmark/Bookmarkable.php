<?php

namespace Miladimos\Social\Traits\Like;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait Bookmarkable
{
    public function isBookmarkedBy(Model $user): bool
    {
        if (\is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }

            return $this->likers()->where(\config('social.bookmarks.user_foreign_key'), $user->getKey())->exists();
        }

        return false;
    }

    public function bookmarkers(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('social.bookmarks.table'),
            'likeable_id',
            config('social.bookmarks.user_foreign_key')
        )
            ->where('likeable_type', $this->getMorphClass());
    }
}
