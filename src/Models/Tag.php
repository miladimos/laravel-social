<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    // protected $fillable = ['name', 'slug', 'deleted_at'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('social.tags.table', 'social_tags');
        
        parent::__construct($attributes);
    }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
