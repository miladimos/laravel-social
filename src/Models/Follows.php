<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.follows.table');
    }

    public function followerable()
    {
        return $this->morphTo();
    }

    public function followingable()
    {
        return $this->morphTo();
    }

    public function needApprove()
    {
        return false;
    }

    public function scopeApproved($query, $s = true)
    {
        return $query->where('approved', $s);
    }
}
