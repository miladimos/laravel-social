<?php

namespace Miladimos\Social\Facades;

use Illuminate\Support\Facades\Facade;

class SocialFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'social';
    }
}
