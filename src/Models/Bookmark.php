<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Bookmark extends Model
{
    protected $table = 'bookmarks';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = \config('social.bookmarks.table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function ($bookmark) {
            $userForeignKey = \config('social.bookmarks.user_foreign_key');
            $bookmark->{$userForeignKey} = $bookmark->{$userForeignKey} ?: auth()->id();

            if (\config('social.bookmarks.uuids')) {
                $bookmark->{$bookmark->getKeyName()} = $bookmark->{$bookmark->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
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
    public function user()
    {
        return $this->belongsTo(\config('auth.providers.users.model'), \config('social.bookmarks.user_foreign_key'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookmarkr()
    {
        return $this->user();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('social.bookmarksable_type', app($type)->getMorphClass());
    }
}
