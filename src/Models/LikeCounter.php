<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class LikeCounter extends Model
{
    protected $table = config('social.likes.table', 'likes');

    protected $guarded = [];

}
