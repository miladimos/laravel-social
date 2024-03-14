<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follows extends Model
{
    protected $guarded = [];

    protected $dates = ['accepted_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.follows.table', 'social_follows');
    }

    // follower
    public function followable(): MorphTo 
    {
        return $this->morphTo();
    }

    // following
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
