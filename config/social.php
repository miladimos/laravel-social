<?php

return [

    'likes' => [

        'model' => Miladimos\Social\Models\Like::class,

        'table' => 'likes',

        'pivot_table' => 'taggables',

        'counter_model' => Miladimos\Social\Models\LikeCounter::class,

        'counter_table' => 'like_counters',

        'morphs' => 'likeable',

        'liker_foreign_key' => 'liker_id',

    ],

    'bookmarks' => [

        'table' => 'bookmarks',

        'model' => Miladimos\Social\Models\Bookmark::class,

        'morphs' => 'bookmarkable',

        'bookmarker_foreign_key' => 'user_id',
    ],

    'categories' => [

        'table' => 'categories',

        'pivot_table' => 'categoriables',

        'model' => Miladimos\Social\Models\Category::class,

        'morphs' => 'categoriable',

        'default_active' => true,
    ],

    'comments' => [

        'table' => 'comments',

        'model' => Miladimos\Social\Models\Comment::class,

        'commentors' => [],

        'morphs' => 'commentable',

        'commentor_foreign_key' => 'commentor_id',

        'default_commentator' => config('auth.providers.users.model'),

        'middleware'   => ['web'],
    ],

    'follows' => [

        'table' => 'follows',

        'morphs' => 'followable',

        'model' => Miladimos\Social\Models\Follows::class,
    ],

    'subscriptions' => [

        'table' => 'subscriptions',

        'morphs' => 'subscriptionable',

        'model' => Miladimos\Social\Models\Subscription::class,

        'subscriber_foreign_key' => 'user_id',

    ],

];
