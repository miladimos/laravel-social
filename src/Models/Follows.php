<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    protected $table = config('social.follows.table');

    protected $guarded = [];

    public $timestamps = true;

    public function likeable()
    {
        return $this->morphTo();
    }
}
