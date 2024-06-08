<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locale
    |--------------------------------------------------------------------------
    |
    | The language in which the files should be created.
    | Available values: en, pt_BR
    |
    */
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Creates
    |--------------------------------------------------------------------------
    |
    | What kind of files it should create
    |
    */
    'creates' => [
        'collection' => true,
        'model' => true,
        'migration' => true,
        'repository' => true,
        'services' => true,
        'controllers' => true,
        'requests' => true,
        'resources' => true,
        'policy' => true,
        'routes' => true,
        'open_api' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Base Class
    |--------------------------------------------------------------------------
    |
    | Configure the base class that must be extended when creating the repository
    |
    | Values:
    | - true: Tries to locate a default repository (for now andregumieri/laravel-repository)
    | - false|null: Do not extends the repository
    | - string: Extends the repository set in the configuration. Ex: "App\Repositories\Base"
    |
    */
    'repository_base_class' => true
];
