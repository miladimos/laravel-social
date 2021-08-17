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


    /**
     * Favorite the given article.
     *
     * @param Model $article
     * @return mixed
     */
    public function favorite(Model $article)
    {
        if (!$this->hasFavorited($article)) {
            return $this->favorites()->attach($article);
        }
    }

    /**
     * Unfavorite the given article.
     *
     * @param Model $article
     * @return mixed
     */
    public function unFavorite(Model $article)
    {
        return $this->favorites()->detach($article);
    }

    /**
     * Get the articles favorited by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Model::class, 'favorites', 'user_id', 'article_id')->withTimestamps();
    }

    /**
     * Check if the user has favorited the given article.
     *
     * @param Model $article
     * @return bool
     */
    public function hasFavorited(Model $article)
    {
        return !!$this->favorites()->where('article_id', $article->id)->count();
    }
}
