<?php
namespace AndreGumieri\LaravelCrud;

use AndreGumieri\LaravelCrud\Console\Commands\Controller;
use AndreGumieri\LaravelCrud\Console\Commands\Crud;
use AndreGumieri\LaravelCrud\Console\Commands\Repository;
use AndreGumieri\LaravelCrud\Console\Commands\RepositoryContract;
use AndreGumieri\LaravelCrud\Console\Commands\Resource;
use AndreGumieri\LaravelCrud\Console\Commands\Service;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                Controller::class,
                Crud::class,
                Repository::class,
                RepositoryContract::class,
                Resource::class,
                Service::class
            ]);
        }
    }
}