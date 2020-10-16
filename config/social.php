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


    ]

];
