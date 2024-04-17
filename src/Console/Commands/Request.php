<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class Request extends RequestMakeCommand
{
    protected function buildClass($name)
    {
        $replaces = [];

        $withModel = false;
        if($routeModel = $this->option('route-model')) {
            $withModel = true;
            $replaces['{{routeModel}}'] = $routeModel;
        }

        $authorize = 'true';
        if($this->option('policy')) {
            if($withModel) {
                $authorize = sprintf('$this->user()->can(\'%s\', $this->model())', $this->option('policy'));
            } else {
                $authorize = sprintf('$this->user()->can(\'%s\')', $this->option('policy'));
            }
        }

        $replaces['{{authorize}}'] = $authorize;


        $class = str_replace(
            array_keys($replaces), array_values($replaces), parent::buildClass($name)
        );

        return $class;
    }

    protected function getStub()
    {
        if($type = $this->option('type')) {
            return $this->resolveStubPath("/stubs/request.{$type}.stub");
        }
        return parent::getStub();
    }

    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['policy', 'p', InputOption::VALUE_OPTIONAL, 'Informs the policy for authorization'];
        $options[] = ['type', null, InputOption::VALUE_REQUIRED, 'Manually specify the controller stub file to use'];
        $options[] = ['route-model', null, InputOption::VALUE_REQUIRED, 'Specify the route model that is binded'];
        return $options;
    }

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