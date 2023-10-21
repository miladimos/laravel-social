<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bookmark extends Model
{
    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.bookmarks.table');
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
