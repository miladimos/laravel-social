<?php

namespace Miladimos\Social\Facades;

use Illuminate\Support\Facades\Facade;

class Social extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'social';
    }
}
