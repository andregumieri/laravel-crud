<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Crud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {singular} {plural?} {--locale=en}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria controller, service, repository, model, migration, policy';

    const TRANSLATE = [
        'pt_BR' => [
            'CreateService' => 'CriarService',
            'DeleteService' => 'DeletarService',
            'UpdateService' => 'AlterarService',
            'ViewService' => 'VerService',
            'ListService' => 'ListarService',

            'CreateController' => 'CriarController',
            'DeleteController' => 'DeletarController',
            'UpdateController' => 'AlterarController',
            'ViewController' => 'VerController',
            'ListController' => 'ListarController',

            'CreateRequest' => 'CriarRequest',
            'DeleteRequest' => 'DeletarRequest',
            'UpdateRequest' => 'AlterarRequest',
            'ViewRequest' => 'VerRequest',
            'ListRequest' => 'ListarRequest',

            'list' => 'listar',
            'view' => 'ver',
            'create' => 'criar',
            'update' => 'alterar',
            'delete' => 'deletar',
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $singularClass = $this->argument('singular');
        $pluralClass = $this->argument('plural') ?? $singularClass . 's';
        $singularString = (string)Str::of($singularClass)->lower();
        $pluralString = (string)Str::of($pluralClass)->lower();

        Artisan::call(sprintf('make:collection %sCollection', $singularClass));

        Artisan::call('make:model ' . $singularClass . ' -m');

        Artisan::call('make:repository ' . $singularClass);

        foreach(['CreateService', 'DeleteService', 'UpdateService', 'ViewService', 'ListService'] as $key) {
            Artisan::call(sprintf('make:service %s/%s -r %sRepository', $pluralClass, $this->string($key), $singularClass));
        }

        foreach(['CreateController', 'DeleteController', 'UpdateController', 'ViewController', 'ListController'] as $key) {
            Artisan::call(sprintf('make:controller %s/%s --type=service --with-resource=%s/%s', $pluralClass, $this->string($key), $singularClass, $singularClass));
        }

        foreach(['CreateRequest', 'DeleteRequest', 'UpdateRequest', 'ViewRequest', 'ListRequest'] as $key) {
            $gate = $this->string((string)Str::of($key)->replaceEnd('Request', '')->kebab());
            Artisan::call(sprintf('make:request %s/%s -g %s-%s', $pluralClass, $this->string($key), Str::of($singularClass)->kebab(), $gate));
        }

        Artisan::call(sprintf('make:resource %s/%s', $singularClass, $singularClass));
        Artisan::call(sprintf('make:resource %s/%sCollection', $singularClass, $singularClass));

        Artisan::call(sprintf('make:policy %sPolicy --model=%s', $singularClass, $singularClass));

        // @todo autoadd to file
        $this->alert('Gates: ' . app_path('Providers/AuthServiceProvider.php'));
        $this->line(sprintf('Gate::define(\'%s-%s\', [%sPolicy::class, \'list\']);', Str::of($singularClass)->kebab(), $this->string('list'), $singularClass));
        $this->line(sprintf('Gate::define(\'%s-%s\', [%sPolicy::class, \'view\']);', Str::of($singularClass)->kebab(), $this->string('view'), $singularClass));
        $this->line(sprintf('Gate::define(\'%s-%s\', [%sPolicy::class, \'create\']);', Str::of($singularClass)->kebab(), $this->string('create'), $singularClass));
        $this->line(sprintf('Gate::define(\'%s-%s\', [%sPolicy::class, \'update\']);', Str::of($singularClass)->kebab(), $this->string('update'), $singularClass));
        $this->line(sprintf('Gate::define(\'%s-%s\', [%sPolicy::class, \'delete\']);', Str::of($singularClass)->kebab(), $this->string('delete'), $singularClass));

        // @todo autoadd to file
        $this->alert('Routes: ' . base_path('routes/api.php'));
        $this->line(sprintf('Route::prefix(\'%s\')->middleware(\'auth:api\')->group(function() {', Str::of($pluralClass)->kebab()));
        $this->line("\t" . sprintf('Route::get(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('ListController')));
        $this->line("\t" . sprintf('Route::post(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('CreateController')));
        $this->line("\t" . sprintf('Route::put(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('UpdateController')));
        $this->line("\t" . sprintf('Route::get(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('ViewController')));
        $this->line("\t" . sprintf('Route::delete(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('DeleteController')));
        $this->line('});');

        // @todo autoadd to file
        $this->alert('AppServiceProvider: ' . app_path('Providers/AppServiceProvider.php'));
        $this->line(sprintf('\\App\\Repositories\\%s\\Contracts\\%sRepository:class => \\App\\Repositories\\%s\\%sRepository::class,', $singularClass, $singularClass, $singularClass, $singularClass));
    }

    private function string($key) {
        if(!isset(self::TRANSLATE[$this->option('locale')])) {
            return $key;
        }

        return self::TRANSLATE[$this->option('locale')][$key];
    }
}
