<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('social.categories.table', 'categories');
    }

    public function categoriable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id')->withDefault(['title' => '---']);
    }

    /**
     * Returns a list of the children categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    // /**
    //  * Get all of the books that are assigned this tag.
    //  */
    // public function books()
    // {
    //     return $this->morphedByMany(Book::class, 'categoriable', 'categoriables');
    // }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

      /**
     * @return mixed
     */
    public function categories(): MorphTo
    {
        return $this->morphTo();
    }
    /**
     * @return collection
     */
    public function entries(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'model', 'categories_models');
    }

    /**
     * @return array
     */
    public static function tree(): array
    {
        return static::get()->toTree()->toArray();
    }

    /**
     * @return static
     */
    public static function findByName(string $name): self
    {
        return static::where('name', $name)->orWhere('slug', $name)->firstOrFail();
    }

    /**
     * @return static
     */
    public static function findById(int $id): self
    {
        return static::findOrFail($id);
    }


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
