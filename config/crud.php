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
    | Repository Base Class
    |--------------------------------------------------------------------------
    |
    | Configure the base class that must be extended when creating the repository
    |
    | Values:
    | - true: Tries to locate a default repository (for now andregumieri/laravel-repository)
    | - false|null: Does not extend the repository
    | - string: Extends the repository set in the configuration. Ex: "App\Repositories\Base"
    |
    */
    'repository_base_class' => true
];
