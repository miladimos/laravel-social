<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    protected $table = 'follows';

    protected $guarded = [];

    public $timestamps = true;

    public function followable()
    {
        return $this->morphTo();
    }
}
