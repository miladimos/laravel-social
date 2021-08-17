<?php

namespace Miladimos\Social\Traits\Category;

use Miladimos\Social\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Categoriable
{
    protected static function bootCategoriable()
    {
        static::deleted(function ($categoriable) {
            foreach ($categoriable->categories as $category) {
                $category->forceDelete();
            }
        });
    }

    public function categoriableModel(): string
    {
        return config('social.categories.model');
    }

    public function categories()
    {
        return $this->categoriesRelation();
    }

    public function categoriesRelation(): MorphToMany
    {
        return $this->morphToMany($this->categoriableModel(), 'categoriable');
    }

    public function syncCategories(...$categories)
    {
        $this->categoriesRelation()->sync($categories);
        $this->save();
    }

    // public function removeCategories()
    // {
    //     $this->CategoriesRelation()->detach();
    // }

    /**
     * @return array (good choice for dropdowns)
     */
    public function categoriesList(): array
    {
        return $this->categories()
            ->pluck('name', 'id')
            ->toArray();
    }


    /**
     * @param $categories
     *
     * @return instance
     */
    public function attachCategory(...$categories)
    {
        $categories = collect($categories)
            ->flatten()
            ->map(function ($category) {
                return $this->getStoredCategory($category);
            })
            ->all();

        $this->categories()->saveMany($categories);

        return $this;
    }

    /**
     * @param $category
     *
     * @return mixed
     */
    public function detachCategory($category)
    {
        $this->categories()->detach($this->getStoredCategory($category));
    }

    /**
     * @param $categories . list of params or an array of parameters
     *
     * @return bool
     */
    public function hasCategory($categories)
    {
        if (is_string($categories)) {
            return $this->categories->contains('name', $categories);
        }

        if ($categories instanceof Category) {
            return $this->categories->contains('id', $categories->id);
        }

        if (is_array($categories)) {
            foreach ($categories as $category) {
                if ($this->hasCategory($category)) {
                    return true;
                }
            }

            return false;
        }

        return $categories->intersect($this->categories)->isNotEmpty();
    }

    /**
     * @param $categories
     *
     * @return bool
     */
    public function hasAnyCategory($categories)
    {
        return $this->hasCategory($categories);
    }

    /**
     * @param $categories . list of params or an array of parameters
     *
     * @return mixed
     */
    public function hasAllCategories($categories)
    {
        if (is_string($categories)) {
            return $this->categories->contains('name', $categories);
        }

        if ($categories instanceof Category) {
            return $this->categories->contains('id', $categories->id);
        }

        $categories = collect()->make($categories)->map(function ($category) {
            return $category instanceof Category ? $category->name : $category;
        });

        return $categories->intersect($this->categories->pluck('name')) === $categories;
    }

    /**
     * @param $category
     *
     * @return instance
     */
    protected function getStoredCategory($category): Category
    {
        if (is_numeric($category)) {
            return app(Category::class)->findById($category);
        }

        if (is_string($category)) {
            return app(Category::class)->findByName($category);
        }

        return $category;
    }
}
