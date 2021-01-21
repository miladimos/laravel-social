<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //    protected $table = config('social.likes.table', 'likes');
    protected $table = 'likes';

    // protected $fillable = ['user_id', 'likeable_id', 'likeable_type'];

    protected $guarded = [];

    public $timestamps = true;

    public function likeable()
    {
        return $this->morphTo();
    }
}
