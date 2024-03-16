<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\HasUUID;
use Miladimos\Social\Models\Bookmark;

class BookmarkGroup extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = config('social.bookmarks.bookmark_groups.table');

        parent::__construct($attributes);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Bookmark::class);
    }
}
