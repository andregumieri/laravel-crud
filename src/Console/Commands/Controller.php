<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class Controller extends ControllerMakeCommand
{
    protected function buildClass($name)
    {
        $replaces = [];
        if(Str::of($this->option('type'))->startsWith('service')) {
            $nameBase = preg_replace('/Controller$/', '', $name);
            $replaces = [
                '{{serviceNamespace}}' => $this->rootNamespace() . 'Services\\' . class_basename($this->getNamespace($nameBase)),
                '{{serviceClass}}' => class_basename($nameBase) . 'Service',
                '{{requestNamespace}}' => $this->rootNamespace() . 'Http\\Requests\\' . class_basename($this->getNamespace($nameBase)),
                '{{requestClass}}' => class_basename($nameBase) . 'Request',
            ];
        }

        if($resource = $this->option('with-resource')) {
            $resourceName = explode('/', $resource);
            $resourceName = array_pop($resourceName);

            $replaces = array_merge($replaces, [
                '{{resourceClassPath}}' => str_replace('/', '\\', $resource),
                '{{resourceClass}}' => $resourceName,
            ]);
        }

        $class = str_replace(
            array_keys($replaces), array_values($replaces), parent::buildClass($name)
        );

        return $class;
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

    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['with-resource', null, InputOption::VALUE_OPTIONAL, 'Inform the resource to the controller type service'];
        return $options;
    }
}
