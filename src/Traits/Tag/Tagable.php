<?php

namespace Miladimos\Social\Traits\Tag;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Tagable
{
    // protected static function bootTagable()
    // {
    //     static::deleted(function ($tagables) {
    //         foreach ($tagables->tags as $category) {
    //             $category->delete();
    //         }
    //     });
    // }

    /**
     * @return \App\Models\Tag[]
     */
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
        $this->save();
        $this->tagsRelation()->sync($tags);
    }


    // public function removeTags()
    // {
    //     $this->tagsRelation()->detach();
    // }

}
