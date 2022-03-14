<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\HasUUID;

class BookmarkGroup extends Model
{
    use HasUUID;

    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.bookmarks.bookmark_groups.table');
    }

  public function bookmarks()
  {
      return $this->belongsToMany(Bookmark::class);
  }
}
