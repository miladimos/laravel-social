<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\HasUUID;

class Bookmark extends Model
{
    use HasUUID;

    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.bookmarks.table');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function bookmarkable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookmarker()
    {
        return $this->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\config('auth.providers.users.model'), \config('social.bookmarks.user_foreign_key'));
    }

    public function groups()
    {
        return $this->belongsToMany(BookmarkGroup::class);
    }
}
