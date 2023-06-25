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
        return $this->morphToMany(config('social.tags.model'), 'taggables', '');
    }

    public function syncTags(array $tags)
    {
        $this->tagsRelation()->sync($tags);
        $this->save();
    }
}
