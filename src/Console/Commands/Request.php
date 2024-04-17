<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class Request extends RequestMakeCommand
{
    protected function buildClass($name)
    {
        $replaces = [];

        $authorize = 'false';
        if($this->option('gate')) {
            $authorize = sprintf('Gate::allows(\'%s\')', $this->option('gate'));
        }

        $replaces = [
            '{{authorize}}' => $authorize,
        ];

        $class = str_replace(
            array_keys($replaces), array_values($replaces), parent::buildClass($name)
        );

        return $class;
    }

    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['gate', 'g', InputOption::VALUE_OPTIONAL, 'Informs the gate for authorization'];
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