<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:service')]
class Service extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:service';

    protected $type = "Service";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    protected function getStub()
    {
        $stub = '/stubs/service.stub';
        if($type = $this->option('type')) {
            $stub = "/stubs/service.{$type}.stub";
        }

        if($this->option('with-repository')) {
            $stub = str_replace('.stub', '.with-repository.stub', $stub);
        }

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        $repository = $this->option('repository');
        $namespace = $this->rootNamespace() . 'Repositories\\' . Str::of($repository)->replaceMatches('/Repository$/i', '')->studly() . '\\' . str_replace('/', '\\', $repository);

        $action = Str::of(class_basename($name))->replaceMatches('/Service$/', '')->camel();

        if($this->option('repository-action')) {
            $action = $this->option('repository-action');
        }

        $replaces = [
            '{{repositoryUsePath}}' => $namespace,
            '{{repositoryClass}}' => class_basename($namespace),
            '{{action}}' => $action,
            '{{modelClass}}' => Str::of(class_basename($namespace))->replaceMatches('/Repository$/', '')->studly(),
            '{{modelUsePath}}' => $this->rootNamespace() . 'Models\\' . Str::of(class_basename($namespace))->replaceMatches('/Repository$/', '')->studly()
        ];

        if($request = $this->option('request')) {
            $replaces['{{requestClass}}'] = class_basename($request);
            $replaces['{{requestNamespace}}'] = $this->rootNamespace() . 'Http\\Requests\\' . str_replace("/", '\\', ltrim($request, '\\/'));
        }

        return str_replace(
            array_keys($replaces), array_values($replaces), parent::buildClass($name)
        );
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }


    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['with-repository', null, InputOption::VALUE_NONE, 'Informs the service that it should create repository'];
        $options[] = ['repository', 'r', InputOption::VALUE_REQUIRED, 'Informs the service whats the repository to load'];
        $options[] = ['repository-action', null, InputOption::VALUE_OPTIONAL, 'Informs the repository action'];
        $options[] = ['request', null, InputOption::VALUE_REQUIRED, 'Informs the request'];
        $options[] = ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'];
        $options[] = ['type', null, InputOption::VALUE_REQUIRED, 'Manually specify the controller stub file to use'];
        return $options;
    }
}
