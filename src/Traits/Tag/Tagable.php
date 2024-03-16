<?php

namespace Miladimos\Social\Traits\Tag;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Model;

trait Taggable
{
    protected static function booted()
    {
        static::deleted(function (Model $entityModel) {
            foreach ($entityModel->tags as $tag) {
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
        return $this->morphToMany(config('social.tags.model'), config('social.tags.morphs'), config('social.tags.morphs_table'));
    }

    public function attachTag($tag)
    {
        $this->tagsRelation()->attach($tag);
        $this->save();
    }

    public function detachTag($tag)
    {
        $this->tagsRelation()->detach($tag);
        $this->save();
    }

    public function syncTags(array $tags)
    {
        $this->tagsRelation()->sync($tags);
        $this->save();
    }
}
