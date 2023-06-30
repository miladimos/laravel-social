<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'tags';

    protected $fillable = ['name', 'slug', 'deleted_at'];

    // public function __construct()
    // {
    //     parent::__construct();

    //     $this->table = config('social.tags.table', 'tags');
    // }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
