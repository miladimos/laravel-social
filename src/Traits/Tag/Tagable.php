<?php

namespace Miladimos\Social\Traits\Tag;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Tagable
{
    protected static function booted()
    {
        static::deleted(function ($tagable) {
            foreach ($tagable->tags as $tag) {
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
        return $this->morphToMany(config('social.tags.model'), 'tagables');
    }

    public function syncTags(array $tags)
    {
        $this->tagsRelation()->sync($tags);
        $this->save();
    }
}
