<?php

namespace Miladimos\Social\Traits\Bookmark;

use Miladimos\Social\Models\Bookmark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanBookmark
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Bookmark
     */
    public function bookmark(Model $object): Bookmark
    {
        $attributes = [
            'bookmarkable_type' => $object->getMorphClass(),
            'bookmarkable_id' => $object->getKey(),
            config('social.bookmark.user_foreign_key') => $this->getKey(),
        ];

        /* @var \Illuminate\Database\Eloquent\Model $bookmark */
        $bookmark = \app(config('social.bookmark.model'));

        /* @var \Overtrue\LaravelBookmark\Traits\Bookmarkable|\Illuminate\Database\Eloquent\Model $object */
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
        /* @var \Overtrue\LaravelBookmark\Bookmark $relation */
        $relation = \app(config('social.bookmark.model'))
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->where(config('social.bookmark.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            if ($this->relationLoaded('bookmarks')) {
                $this->unsetRelation('bookmarks');
            }

            return $relation->delete();
        }

        return true;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Bookmark|null
     * @throws \Exception
     */
    public function toggleBookmark(Model $object)
    {
        return $this->hasBookmarkd($object) ? $this->unbookmark($object) : $this->bookmark($object);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return bool
     */
    public function hasBookmarkd(Model $object): bool
    {
        return ($this->relationLoaded('bookmarks') ? $this->bookmarks : $this->bookmarks())
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(config('social.bookmark.model'), config('social.bookmark.user_foreign_key'), $this->getKeyName());
    }
}
