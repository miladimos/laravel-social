<?php

namespace Miladimos\Social\Traits\Category;


trait Categoriable
{
    // protected static function bootCategoriable()
    // {
    //     static::deleted(function ($categoriable) {
    //         foreach ($categoriable->categories as $category) {
    //             $category->delete();
    //         }
    //     });
    // }

    /**
     * @return \App\Models\Comment[]
     */
    public function categories()
    {
        return $this->categoriesRelation();
    }

    public function categoriesRelation(): MorphToMany
    {
        return $this->morphToMany(config('social.categories.model'), 'categoriable');
    }

    public function syncCategories(array $categories)
    {
        $this->save();
        $this->categoriesRelation()->sync($categories);
    }


    // public function removeCategories()
    // {
    //     $this->CategoriesRelation()->detach();
    // }

}
