<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RepositoryContract extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository-contract';

    protected $type = 'RepositoryContract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository contract interface';


    protected function getStub()
    {
        $stub = '/stubs/repository-contract.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        if(!Str::of($name)->endsWith('Repository')) {
            $name .= 'Repository';
        }

        $model = str_replace('Repository', '', class_basename($name));

        $replaces = [
            '{{model}}' => $model
        ];

        return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the request already exists'],
        ];
    }

    protected function getNamespace($name)
    {
        $complemento = class_basename($name);
        if(Str::of($complemento)->endsWith('Repository')) {
            $complemento = str_replace('Repository', '', $complemento);
        }

        return parent::getNamespace($name) . '\\' . $complemento . '\\Contracts';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    protected function replaceClass($stub, $name)
    {
        return parent::replaceClass($stub, class_basename($name));
    }

    protected function getPath($name)
    {
        if(!Str::of($name)->endsWith('Repository')) {
            $name .= 'Repository';
        }

        $name = $this->getNamespace($name) . '\\' . class_basename($name);

        return parent::getPath($name);
    }

    public function handle()
    {
        if(parent::handle()) {
            $this->createFactory();
        }
    }

    protected function createContract()
    {
//        $factory = Str::studly($this->argument('name'));
//
//        $this->call('make:factory', [
//            'name' => "{$factory}Factory",
//            '--model' => $this->qualifyClass($this->getNameInput()),
//        ]);
    }
}
