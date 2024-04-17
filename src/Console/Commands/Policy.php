<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class Policy extends PolicyMakeCommand
{
    protected function buildClass($name)
    {
        $class = parent::buildClass($name);

        $replaces = [];

        if($name = $this->option('method-list')) {
            $replaces['{{listMethodName}}'] = $name;
        }

        if($name = $this->option('method-view')) {
            $replaces['{{viewMethodName}}'] = $name;
        }

        if($name = $this->option('method-create')) {
            $replaces['{{createMethodName}}'] = $name;
        }

        if($name = $this->option('method-update')) {
            $replaces['{{updateMethodName}}'] = $name;
        }

        if($name = $this->option('method-delete')) {
            $replaces['{{deleteMethodName}}'] = $name;
        }


        $class = str_replace(
            array_keys($replaces), array_values($replaces), $class
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
        $options[] = ['method-list', null, InputOption::VALUE_REQUIRED, 'Inform the name for method list'];
        $options[] = ['method-view', null, InputOption::VALUE_REQUIRED, 'Inform the name for method view'];
        $options[] = ['method-create', null, InputOption::VALUE_REQUIRED, 'Inform the name for method create'];
        $options[] = ['method-update', null, InputOption::VALUE_REQUIRED, 'Inform the name for method update'];
        $options[] = ['method-delete', null, InputOption::VALUE_REQUIRED, 'Inform the name for method delete'];
        return $options;
    }
}