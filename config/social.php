<?php

// config for miladimos/laravel-social
return [

    'follows' => [
        'model' => Miladimos\Social\Models\Follows::class,
        'need_follows_to_approved' => false,
    ],

    'likes' => [
        'model' => Miladimos\Social\Models\Like::class,
    ],
];
