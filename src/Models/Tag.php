<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table;

    protected $guarded = [];

    // protected $fillable = ['name', 'slug', 'deleted_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.tags.table', 'social_tags');
    }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
