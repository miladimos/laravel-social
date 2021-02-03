<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class LikeCounter extends Model
{
    protected $table = config('social.like_counts.table', 'like_counters');

    protected $guarded = [];

}
