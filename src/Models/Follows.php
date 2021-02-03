<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    //    protected $table = config('social.likes.table', 'likes');
    protected $table = 'likes';

    protected $guarded = [];

    public $timestamps = true;

    public function likeable()
    {
        return $this->morphTo();
    }
}
