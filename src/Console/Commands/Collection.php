<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class Collection extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:collection';

    protected $type = 'Collection';

    protected function getStub()
    {
        $stub = '/stubs/collection.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        $class = class_basename($name);

        if(!Str::of($name)->endsWith('Collection')) {
            $name .= 'Collection';
        }

        $model = str_replace('Collection', '', class_basename($name));

        $replaces = [
            '{{model}}' => $model,
        ];

        return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the request already exists'],
        ];
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Collections';
    }

    protected function getPath($name)
    {
        if(!Str::of($name)->endsWith('Collection')) {
            $name .= 'Collection';
        }

        $name = $this->getNamespace($name) . '\\' . class_basename($name);

        return parent::getPath($name);
    }
}