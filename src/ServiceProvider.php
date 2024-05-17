<?php
namespace AndreGumieri\LaravelCrud;

use AndreGumieri\LaravelCrud\Console\Commands\Collection;
use AndreGumieri\LaravelCrud\Console\Commands\Controller;
use AndreGumieri\LaravelCrud\Console\Commands\Crud;
use AndreGumieri\LaravelCrud\Console\Commands\Model;
use AndreGumieri\LaravelCrud\Console\Commands\Policy;
use AndreGumieri\LaravelCrud\Console\Commands\Repository;
use AndreGumieri\LaravelCrud\Console\Commands\RepositoryContract;
use AndreGumieri\LaravelCrud\Console\Commands\Request;
use AndreGumieri\LaravelCrud\Console\Commands\Resource;
use AndreGumieri\LaravelCrud\Console\Commands\Service;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/crud.php' => config_path('crud.php'),
        ], 'laravel-crud');

        if($this->app->runningInConsole()) {
            $this->commands([
                Collection::class,
                Controller::class,
                Crud::class,
                Model::class,
                Policy::class,
                Repository::class,
                RepositoryContract::class,
                Request::class,
                Resource::class,
                Service::class
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/crud.php', 'crud'
        );
    }
}