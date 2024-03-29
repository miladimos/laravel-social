<?php

// config for miladimos/laravel-social
return [

    'tags' => [
        'model' => Miladimos\Social\Models\Tag::class,
        'table' => 'social_tags',
        'morphs' => 'taggables',
        'morphs_table' => 'social_taggables',
    ],

    // follow/unfollow - subscription/unsubscription or ...
    'follows' => [
        'model' => Miladimos\Social\Models\Follows::class,
        'table' => 'social_follows',
        'followable_morphs' => 'followable', // follower
        'followingable_morphs' => 'followingable', // following
        'need_follows_to_approved' => false,
    ],


    'categories' => [
        'model' => Miladimos\Social\Models\Category::class,

        'morphs' => 'categoriable',
    ],

    'likes' => [
        'model' => Miladimos\Social\Models\Like::class,

        'liker_foreign_key' => 'liker_id',
    ],

    'bookmarks' => [
        'model' => Miladimos\Social\Models\Bookmark::class,

        'morphs' => 'bookmarkable',

        'bookmarker_foreign_key' => 'user_id',

        'bookmark_group' => [
            'model' => Miladimos\Social\Models\BookmarkGroup::class,
        ],
    ],


    'comments' => [
        'model' => Miladimos\Social\Models\Comment::class,

        'commentors' => [],

        'morphs' => 'commentable',

        'commentor_foreign_key' => 'commentor_id',

        'default_commentator' => config('auth.providers.users.model'),

        'middleware'   => ['web'],

        'need_approve' => false,

        'has_rate' => false,
    ],
];
