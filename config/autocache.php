<?php

/**
 * Laravel Autocache Configuration
 */
return [
    /**
     * It's not mandatory to use the APP_KEY, but make sure
     * that you key is unique per project in case you are on
     * a shared redis instance
     */
    'key' => sha1(env('APP_KEY', 'uniq3-k3y-p3r-pr0j3ct')),

    /**
     * List of models that implement autocache by default.
     * Models have to also implement the Autocache trait
     * in order to work properly
     */
    'models' => [
        // App\Model::class,
    ],

    /**
     * Prefix for automatically generated cache keys.
     * It's recomendable to flush the cache before changing this value
     * to avoid dangling cache keys.
     */
    'prefix' => 'autocache-',
];
