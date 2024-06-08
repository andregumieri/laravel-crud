<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Crud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {singular} {plural?} {--locale=} {--repository-base-class=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates controller, service, repository, model, migration, policy';
    private string|array|bool|null $singularClass;
    private string|array|bool $pluralClass;
    private string $singularString;
    private string $pluralString;
    /**
     * @var array|bool|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed|string|null
     */
    private ?string $locale;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->singularClass = $singularClass = $this->argument('singular');
        $this->pluralClass = $pluralClass = $this->argument('plural') ?? $singularClass . 's';
        $this->singularString = $singularString = (string)Str::of($singularClass)->lower();
        $this->pluralString = $pluralString = (string)Str::of($pluralClass)->lower();

        $this->locale = $this->option('locale');
        if(!$this->locale) {
            $this->locale = config('crud.locale');
        }

        if(!$this->locale) {
            $this->locale = 'en';
        }


        // COLLECTION
        if(config('crud.creates.collection')) {
            $this->makeCollection();
        }


        // MODEL
        $this->makeModel();


        // REPOSITORY
        $this->makeRepository();


        // SERVICES
        $this->makeServices();


        // CONTROLLERS
        $this->makeControllers();


        // REQUESTS
        $this->makeRequests();


        // RESOURCES
        $this->makeResources();


        // POLICY
        $this->makePolicy();


        // OUTPUTS MANUAIS
        $this->outputRoutes();
        $this->outputServiceProvider();

        $this->outputOpenApiFile();


    }

    private function outputOpenApiFile()
    {
        $openAPI = [
            'openapi' => '3.0.1',
            'info' => ['title' => 'CRUD ' . Str::of($this->pluralClass)->ucsplit()->join(' '), 'description' => '', 'version' => '1.0.0'],
            'tags' => [],
            'paths' => [],
            'components' => ['schemas' => (object)[], 'securitySchemes' => ['bearer' => ['type' => 'http', 'scheme' => 'bearer']]],
            'servers' => []
        ];

        $base = '/api/' . Str::of($this->pluralClass)->kebab();

        $parameters = [['name' => 'Accept', 'in' => 'header', 'description' => '', 'required' => true, 'example' => 'application/json', 'schema' => ['type' => 'string']]];
        $responses = ['200' => ['description' => 'Success', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => (object)[]]]]]];

        $parametersList = array_merge($parameters, [
            ['name' => 'page', 'in' => 'query', 'description' => __('Page', locale: $this->locale), 'required' => false, 'example' => 1, 'schema' => ['type' => 'number']],
            ['name' => 'per_page', 'in' => 'query', 'description' => __('Items per page', locale: $this->locale), 'required' => false, 'example' => 60, 'schema' => ['type' => 'number']],
        ]);

        $openAPI['paths'][$base] = [
            'post' => ['summary' => __('Create', locale: $this->locale), 'deprecated' => false, 'description' => '', 'tags' => [], 'parameters' => $parameters, 'responses' => $responses, 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => (object)[]], 'example' => (object)[]]]]],
            'get' => ['summary' => __('List', locale: $this->locale), 'deprecated' => false, 'description' => '', 'tags' => [], 'parameters' => $parametersList, 'responses' => $responses]
        ];

        $openAPI['paths'][$base . '/{{id}}'] = [
            'patch' => ['summary' => __('Update', locale: $this->locale), 'deprecated' => false, 'description' => '', 'tags' => [], 'parameters' => $parameters, 'responses' => $responses, 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => (object)[]], 'example' => (object)[]]]]],
            'get' => ['summary' => __('View', locale: $this->locale), 'deprecated' => false, 'description' => '', 'tags' => [], 'parameters' => $parameters, 'responses' => $responses],
            'delete' => ['summary' => __('Delete', locale: $this->locale), 'deprecated' => false, 'description' => '', 'tags' => [], 'parameters' => $parameters, 'responses' => $responses]
        ];

        $filename = Str::of($base)->slug() . '.openapi.json';

        Storage::disk('local')->put($filename, json_encode($openAPI, JSON_UNESCAPED_SLASHES));

        $this->info("Open API file generated at " . Storage::path($filename));
    }

    /**
     * @return void
     */
    public function makeCollection(): void
    {
        Artisan::call(sprintf('make:collection %sCollection', $this->singularClass));
    }

    /**
     * @return void
     */
    public function makeModel(): void
    {
        $flags = [];
        if(config('crud.creates.migration')) {
            $flags[] = '-m';
        }

        if(config('crud.creates.collection')) {
            $flags[] = '--with-collection';
        }

        Artisan::call('make:model ' . $this->singularClass . ' ' . implode(' ', $flags));
    }

    /**
     * @return void
     */
    public function makeRepository(): void
    {
        $extends = '';
        if ($this->option('repository-base-class')) {
            $extends = str_replace("\\", "\\\\", $this->option('repository-base-class'));
        } else {
            if (config('crud.repository_base_class') === true) {
                if (class_exists('AndreGumieri\\LaravelRepository\\Repositories\\Repository')) {
                    $extends = 'AndreGumieri\\\\LaravelRepository\\\\Repositories\\\\Repository';
                }
            } elseif (is_string(config('crud.repository_base_class'))) {
                $extends = str_replace("\\", "\\\\", config('crud.repository_base_class'));
            }
        }
        Artisan::call(sprintf('make:repository --extends=%s %s', $extends, $this->singularClass));
    }

    /**
     * @return string
     */
    public function makeServices(): string
    {
        foreach (['create', 'delete', 'update'] as $key) {
            Artisan::call(sprintf(
                'make:service %s/%s -r %sRepository --request=%s/%s --type=%s --repository-action=%s',
                $this->pluralClass,
                __('laravel-crud::classes.service_' . $key, locale: $this->locale),
                $this->singularClass,
                $this->pluralClass,
                __('laravel-crud::classes.request_' . $key, locale: $this->locale),
                Str::of($key)->replaceEnd('Service', '')->camel(),
                Str::of($key)->replaceEnd('Service', '')->camel()
            ));
        }

        foreach (['list'] as $key) {
            Artisan::call(sprintf(
                'make:service %s/%s -r %sRepository --repository-action=%s --request=%s/%s --type=%s',
                $this->pluralClass,
                __('laravel-crud::classes.service_' . $key, locale: $this->locale),
                $this->singularClass,
                'searchPaginated',
                $this->pluralClass,
                __('laravel-crud::classes.request_' . $key, locale: $this->locale),
                Str::of($key)->replaceEnd('Service', '')->camel()
            ));
        }

        foreach (['view'] as $key) {
            Artisan::call(sprintf(
                'make:service %s/%s -r %sRepository --request=%s/%s --type=%s',
                $this->pluralClass,
                __('laravel-crud::classes.service_' . $key, locale: $this->locale),
                $this->singularClass,
                $this->pluralClass,
                __('laravel-crud::classes.request_' . $key, locale: $this->locale),
                Str::of($key)->replaceEnd('Service', '')->camel()
            ));
        }

        return $key;
    }

    /**
     * @return void
     */
    public function makeControllers(): void
    {
        Artisan::call(sprintf('make:controller %s/%s --type=service --with-resource=%s/%s',
            $this->pluralClass,
            __('laravel-crud::classes.controller_create', locale: $this->locale),
            $this->singularClass,
            $this->singularClass));

        Artisan::call(sprintf('make:controller %s/%s --type=service-update --with-resource=%s/%s --model=%s',
            $this->pluralClass,
            __('laravel-crud::classes.controller_update', locale: $this->locale),
            $this->singularClass,
            $this->singularClass,
            $this->singularClass));

        Artisan::call(sprintf('make:controller %s/%s --type=service-paginated --with-resource=%s/%s',
            $this->pluralClass,
            __('laravel-crud::classes.controller_list', locale: $this->locale),
            $this->singularClass,
            $this->singularClass));

        Artisan::call(sprintf('make:controller %s/%s --type=service-view --with-resource=%s/%s --model=%s',
            $this->pluralClass,
            __('laravel-crud::classes.controller_view', locale: $this->locale),
            $this->singularClass,
            $this->singularClass,
            $this->singularClass));

        Artisan::call(sprintf('make:controller %s/%s --type=service-delete --with-resource=%s/%s --model=%s',
            $this->pluralClass,
            __('laravel-crud::classes.controller_delete', locale: $this->locale),
            $this->singularClass,
            $this->singularClass,
            $this->singularClass));
    }

    /**
     * @return void
     */
    public function makeRequests(): void
    {
        foreach (['delete', 'update', 'view'] as $key) {
            Artisan::call(sprintf('make:request %s/%s -p %s --type=key --route-model=%s',
                $this->pluralClass,
                __('laravel-crud::classes.request_' . $key, locale: $this->locale),
                __($key, locale: $this->locale),
                Str::of($this->singularClass)->camel()));
        }

        foreach (['create', 'list'] as $key) {
            Artisan::call(sprintf('make:request %s/%s -p %s --type=plain --route-model=%s',
                $this->pluralClass,
                __('laravel-crud::classes.request_' . $key, locale: $this->locale),
                __($key, locale: $this->locale),
                Str::of($this->singularClass)->camel()));
        }
    }

    /**
     * @return void
     */
    public function makeResources(): void
    {
        Artisan::call(sprintf('make:resource %s/%s', $this->singularClass, $this->singularClass));
        Artisan::call(sprintf('make:resource %s/%sCollection', $this->singularClass, $this->singularClass));
    }

    /**
     * @param bool|array|string|null $singularClass
     * @return void
     */
    public function makePolicy(): void
    {
        Artisan::call(sprintf('make:policy %sPolicy --model=%s --method-list=%s --method-view=%s --method-create=%s --method-update=%s --method-delete=%s',
            $this->singularClass,
            $this->singularClass,
            __('list', locale: $this->locale),
            __('view', locale: $this->locale),
            __('create', locale: $this->locale),
            __('update', locale: $this->locale),
            __('delete', locale: $this->locale)));
    }

    /**
     * @return void
     */
    public function outputRoutes(): void
    {
        $this->alert('Routes: ' . base_path('routes/api.php'));

        $this->line(sprintf('Route::prefix(\'%s\')->middleware(\'auth:api\')->group(function() {',
            Str::of($this->pluralClass)->kebab()));

        $this->line("\t" . sprintf('Route::get(\'/\', \App\Http\Controllers\%s\%s::class);',
                $this->pluralClass,
                __('laravel-crud::classes.controller_list', locale: $this->locale)));

        $this->line("\t" . sprintf('Route::post(\'/\', \App\Http\Controllers\%s\%s::class);',
                $this->pluralClass,
                __('laravel-crud::classes.controller_create', locale: $this->locale)));

        $this->line("\t" . sprintf('Route::patch(\'/{%s}\', \App\Http\Controllers\%s\%s::class);',
                Str::of($this->singularClass)->camel(),
                $this->pluralClass,
                __('laravel-crud::classes.controller_update', locale: $this->locale)));

        $this->line("\t" . sprintf('Route::get(\'/{%s}\', \App\Http\Controllers\%s\%s::class);',
                Str::of($this->singularClass)->camel(),
                $this->pluralClass,
                __('laravel-crud::classes.controller_view', locale: $this->locale)));

        $this->line("\t" . sprintf('Route::delete(\'/{%s}\', \App\Http\Controllers\%s\%s::class);',
                Str::of($this->singularClass)->camel(),
                $this->pluralClass,
                __('laravel-crud::classes.controller_delete', locale: $this->locale)));

        $this->line('});');
    }

    /**
     * @return void
     */
    public function outputServiceProvider(): void
    {
        $this->alert('AppServiceProvider: ' . app_path('Providers/AppServiceProvider.php'));
        $this->line(sprintf('\\App\\Repositories\\%s\\Contracts\\%sRepository::class => \\App\\Repositories\\%s\\%sRepository::class,',
            $this->singularClass,
            $this->singularClass,
            $this->singularClass,
            $this->singularClass));
    }
}
