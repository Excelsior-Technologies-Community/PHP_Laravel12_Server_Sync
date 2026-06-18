<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Remote Server Simulation
    |--------------------------------------------------------------------------
    |
    | This replaces real SSH server.
    | In real package this contains:
    | host, user, key, path
    |
    */

    'remote' => [

        'path' => storage_path(
            'sync/remote'
        ),

    ],



    /*
    |--------------------------------------------------------------------------
    | Database Sync
    |--------------------------------------------------------------------------
    */

    'database' => [

        'backup' => storage_path(
            'sync/remote/database/backup.sql'
        ),


        'dump_path' => storage_path(
            'sync/dumps'
        ),


        'exclude' => [

            'cache',

            'sessions',

            'jobs',

        ],

    ],



    /*
    |--------------------------------------------------------------------------
    | File Sync
    |--------------------------------------------------------------------------
    */

    'files' => [

        'source' => storage_path(
            'sync/remote/files'
        ),


        'destination' => storage_path(
            'app'
        ),


        'exclude' => [

            '.env',

            'vendor',

            'node_modules'

        ],

    ],


];