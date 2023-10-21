<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follows extends Model
{
    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.follows.table');
    }

    public function followerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function followingable(): MorphTo
    {
        return $this->morphTo();
    }

    public function needApprove()
    {
        return config('social.follows.need_follows_to_approved');
    }

    public function scopeApproved($query, $s = true)
    {
        return $query->where('approved', $s);
    }
}
