<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Miladimos\Social\Models\BookmarkGroup;

class Bookmark extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = config('social.bookmarks.table');

        parent::__construct($attributes);
    }

    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bookmarkerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bookmarker()
    {
        return $this->user();
    }

    public function user()
    {
        return $this->belongsTo(\config('auth.providers.users.model'), \config('social.bookmarks.user_foreign_key'));
    }

    public function groups()
    {
        return $this->belongsToMany(BookmarkGroup::class);
    }
}
