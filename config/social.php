<?php

return [

    'likes' => [

        'model' => Miladimos\Social\Models\Like::class,

        'table' => 'likes',

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

        'follows_table' => 'follows',

        'morphs' => 'followable',

        'follow_model' => Miladimos\Social\Models\Follows::class,
    ],

    'subscriptions' => [

        'subscriptions_table' => 'subscriptions',

        'morphs' => 'subscriptionable',

        'subscription_model' => Miladimos\Social\Models\Subscription::class,
    ],

];
