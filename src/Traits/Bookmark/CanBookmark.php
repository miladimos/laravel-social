<?php

namespace Miladimos\Social\Traits\Bookmark;

use Miladimos\Social\Models\Bookmark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanBookmark
{

    public function bookmark(Model $object): Bookmark
    {
        $attributes = [
            'bookmarkable_type' => $object->getMorphClass(),
            'bookmarkable_id' => $object->getKey(),
            config('social.bookmarks.user_foreign_key') => $this->getKey(),
        ];

        $bookmark = \app(config('social.bookmarks.model'));

        return $bookmark->where($attributes)->firstOr(
            function () use ($bookmark, $attributes) {
                $bookmark->unguard();

                if ($this->relationLoaded('bookmarks')) {
                    $this->unsetRelation('bookmarks');
                }

                return $bookmark->create($attributes);
            }
        );
    }

    public function unbookmark(Model $object): bool
    {
        $relation = \app(config('social.bookmarks.model'))
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->where(config('social.bookmarks.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            if ($this->relationLoaded('bookmarks')) {
                $this->unsetRelation('bookmarks');
            }

            return $relation->delete();
        }

        return true;
    }

    public function toggleBookmark(Model $object)
    {
        return $this->hasBookmarkd($object) ? $this->unbookmark($object) : $this->bookmark($object);
    }

    public function hasBookmarkd(Model $object): bool
    {
        return ($this->relationLoaded('bookmarks') ? $this->bookmarks : $this->bookmarks())
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(config('social.bookmarks.model'), config('social.bookmarks.user_foreign_key'), $this->getKeyName());
    }
}
