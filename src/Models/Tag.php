<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Miladimos\Social\Traits\HasUUID;

class Tag extends Model
{
    use HasUUID;

    protected $table;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('social.tags.table', 'tags');
    }

    public function tagables(): MorphTo
    {
        return $this->morphTo();
    }

    // /**
    //  * Get all of the books that are assigned this tag.
    //  */
    // public function books()
    // {
    //     return $this->morphedByMany(Book::class, 'tagables', 'tagabless');
    // }
}
