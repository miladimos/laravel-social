<?php

return [

    // Likes feature configs
    'likes' => [

        /*
         * likes Model.
         */
        'model' => Miladimos\Social\Models\Like::class,

        /*
        * likes table name.
        */
        'table' => 'likes',

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
