<?php

// config for miladimos/laravel-social
return [

    'follows' => [
        'model' => Miladimos\Social\Models\Follows::class,
        'table' => 'social_follows',
        'followable_morphs' => 'followable', // follower
        'followingable_morphs' => 'followingable', // following
        'need_follows_to_approved' => false,
    ],


    'likes' => [
        'model' => Miladimos\Social\Models\Like::class,

        'liker_foreign_key' => 'liker_id',
    ],
];
