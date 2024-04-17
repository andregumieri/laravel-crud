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

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        $repository = $this->option('repository');
        $namespace = $this->rootNamespace() . 'Repositories\\' . str_replace('/', '\\', $repository);

        $replaces = [
            '{{repositoryUsePath}}' => $namespace,
            '{{repositoryClass}}' => class_basename($namespace),
            '{{action}}' => Str::of(class_basename($name))->replaceMatches('/Service$/', '')->camel(),
            '{{modelClass}}' => Str::of(class_basename($namespace))->replaceMatches('/Repository$/', '')->studly(),
            '{{modelUsePath}}' => $this->rootNamespace() . 'Models\\' . Str::of(class_basename($namespace))->replaceMatches('/Repository$/', '')->studly()
        ];

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
        $options[] = ['repository', 'r', InputOption::VALUE_REQUIRED, 'Informs the service whats the repository to load'];
        $options[] = ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'];
        return $options;
    }
}
