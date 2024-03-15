<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $guarded = [];

    public function __construct()
    {
        $this->table = config('social.categories.table', 'social_categories');

        parent::__construct();
    }

    public function categoriable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id')->withDefault(['title' => '---']);
    }

    function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public static function findByTitle(string $name): self
    {
        return static::where('name', $name)->orWhere('slug', $name)->firstOrFail();
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
}
