<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class Repository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    protected $type = 'Repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';


    protected function getStub()
    {
        $stub = '/stubs/repository.stub';

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
            '{{model}}' => $model,
            '{{baseRepositoryUse}}' => '',
            '{{extends}}' => '',
        ];

        if($this->option('extends')) {
            $replaces['{{baseRepositoryUse}}'] = 'use ' . $this->option('extends') . ' as BaseRepository;';
            $replaces['{{extends}}'] = ' extends BaseRepository';
        }

        return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the request already exists'],
            ['extends', null, InputOption::VALUE_OPTIONAL, 'What repository it should extends from'],
        ];
    }

    protected function getNamespace($name)
    {
        $complemento = class_basename($name);
        if(Str::of($complemento)->endsWith('Repository')) {
            $complemento = str_replace('Repository', '', $complemento);
        }

        return parent::getNamespace($name) . '\\' . $complemento;
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
        if(parent::handle() !== false) {
            $this->createContract();
        }
    }

    protected function createContract()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('make:repository-contract', [
            'name' => "{$factory}"
        ]);
    }
}
