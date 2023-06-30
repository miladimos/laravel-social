<?php

namespace Miladimos\Social\Traits\Tag;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    protected static function booted()
    {
        static::deleted(function ($taggable) {
            foreach ($taggable->tags as $tag) {
                $tag->forceDelete();
            }
        });
    }

    public function tags()
    {
        return $this->tagsRelation();
    }

    public function tagsRelation(): MorphToMany
    {
        return $this->morphToMany(config('social.tags.model'), config('social.tags.taggables'), config('social.tags.morphs'));
    }

    public function attach($tag)
    {
        $this->tagsRelation()->attach($tag);
        $this->save();
    }

    public function detach($tag)
    {
        $this->tagsRelation()->detach($tag);
        $this->save();
    }

    public function syncTags(array $tags)
    {
        $this->tagsRelation()->sync($tags);
        $this->save();
    }

    // /**
    //  * Get all of the books that are assigned this tag.
    //  */
    // public function books()
    // {
    //     return $this->morphedByMany(Book::class, 'tagables', 'tagabless');
    // }
}
