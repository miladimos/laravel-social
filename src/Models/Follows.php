<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follows extends Model
{
    protected $guarded = [];

    protected $dates = ['accepted_at'];
1
    public function __construct(array $attributes = [])
    {
        $this->table = config('social.follows.table', 'social_follows');

        parent::__construct($attributes);
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

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }
}
