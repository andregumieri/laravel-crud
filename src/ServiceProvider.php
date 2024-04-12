<?php

use Andregumieri\LaravelCrud\Console\Commands\Crud;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([Crud::class]);
        }
    }
}