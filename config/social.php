<?php

return [

    // Likes feature configs
    'likes' => [

        /*
         * likes Model.
         */
        'likes_model' => Miladimos\Social\Models\Like::class,

        /*
         * likes counter Model.
         */
        'likes_counter_model' => Miladimos\Social\Models\LikeCounter::class,

        /*
        * likes table name.
        */
        'likes_table' => 'likes',

        /*
        * likes counter table name.
        */
        'likes_counter_table' => 'like_counters',

        /*
        * user foreign id column name.
        */
        'user_id' => 'user_id',

        /*
        * likes morphs relations name.
        */
        'morphs' => 'likeable',

        /*
     * User tables foreign key name.
     */
        'user_foreign_key' => 'user_id',

        /*
     * Table name for likes records.
     */
        'likes_table' => 'likes',

        /*
     * Model name for like record.
     */
        'like_model' => Miladimos\Social\Models\Like::class,


    ],

    'bookmark' => [
        /*
            * Table name for subscriptions records.
            */
        'bookmarks_table' => 'bookmarks',

        /*
                * Model name for Subscribe record.
                */
        'bookmark_model' => Miladimos\Social\Models\Bookmark::class,
    ],
    'comments' => [
        /*
            * Table name for subscriptions records.
            */
        'comments_table' => 'comments',

        /*
                * Model name for Subscribe record.
                */
        'comment_model' => Miladimos\Social\Models\Comment::class,
    ],
    'follows' => [
        /*
            * Table name for subscriptions records.
            */
        'subscriptions_table' => 'subscriptions',

        /*
                * Model name for Subscribe record.
                */
        'subscription_model' => Miladimos\Social\Models\Follows::class,
    ],

    'subscriptions' => [
        /*
            * Table name for subscriptions records.
            */
        'subscriptions_table' => 'subscriptions',

        /*
            * Model name for Subscribe record.
            */
        'subscription_model' => Miladimos\Social\Models\Subscription::class,
    ],

];
