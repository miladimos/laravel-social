<?php

namespace Miladimos\Social\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use JohnDoe\BlogPackage\Models\User;

class UserUnFollowed
{
    use Dispatchable, SerializesModels;

    public function __construct(public User $user)
    {
        //
    }
}
