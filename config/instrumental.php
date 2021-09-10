<?php

return [

    /*
     * Enabled
     */
    'enabled' => env('INSTRUMENTAL_ENABLED', false),

    /*
     * Api
     */
    'api' => [
        'key' => env('INSTRUMENTAL_TOKEN', ''),
    ],

    /*
     * APM APP Name
     * ------------
     * This will be used as a prefix in metrics
     */
    'app' => [
        'name' => env('INSTRUMENTAL_APP', 'laravel'),
    ],

];