<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;

class Controller extends ControllerMakeCommand
{
    protected function buildClass($name)
    {
        $replaces = [];
        if($this->option('type') == 'service') {
            $nameBase = preg_replace('/Controller$/', '', $name);
            $replaces = [
                '{{serviceNamespace}}' => $this->rootNamespace() . 'Services\\' . class_basename($this->getNamespace($nameBase)),
                '{{serviceClass}}' => class_basename($nameBase) . 'Service',
                '{{requestNamespace}}' => $this->rootNamespace() . 'Http\\Requests\\' . class_basename($this->getNamespace($nameBase)),
                '{{requestClass}}' => class_basename($nameBase) . 'Request',
            ];
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
}
