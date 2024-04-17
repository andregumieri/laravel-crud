<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;

class Policy extends PolicyMakeCommand
{
    protected function resolveStubPath($stub)
    {
        if(file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))) {
            return $customPath;
        } else if (file_exists(__DIR__.$stub)) {
            return __DIR__.$stub;
        } else {
            return parent::resolveStubPath($stub);
        }
    }
}