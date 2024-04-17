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


        // COLLECTION
        Artisan::call(sprintf('make:collection %sCollection', $singularClass));


        // MODEL
        Artisan::call('make:model ' . $singularClass . ' -m');


        // REPOSITORY
        Artisan::call('make:repository ' . $singularClass);


        // SERVICES
        foreach(['CreateService', 'DeleteService', 'UpdateService'] as $key) {
            Artisan::call(sprintf('make:service %s/%s -r %sRepository --request=%s/%s --type=%s --repository-action=%s', $pluralClass, $this->string($key), $singularClass, $pluralClass, $this->string(Str::of($key)->replaceEnd('Service', 'Request')), Str::of($key)->replaceEnd('Service', '')->camel(), Str::of($key)->replaceEnd('Service', '')->camel()));
        }

        foreach(['ListService'] as $key) {
            Artisan::call(sprintf('make:service %s/%s -r %sRepository --repository-action=%s --request=%s/%s --type=%s', $pluralClass, $this->string($key), $singularClass, 'searchPaginated', $pluralClass, $this->string(Str::of($key)->replaceEnd('Service', 'Request')), Str::of($key)->replaceEnd('Service', '')->camel()));
        }

        foreach(['ViewService'] as $key) {
            Artisan::call(sprintf('make:service %s/%s -r %sRepository --request=%s/%s --type=%s', $pluralClass, $this->string($key), $singularClass, $pluralClass, $this->string(Str::of($key)->replaceEnd('Service', 'Request')), Str::of($key)->replaceEnd('Service', '')->camel()));
        }



        // CONTROLLERS
        foreach(['CreateController'] as $key) {
            Artisan::call(sprintf('make:controller %s/%s --type=service --with-resource=%s/%s', $pluralClass, $this->string($key), $singularClass, $singularClass));
        }

        foreach(['UpdateController'] as $key) {
            Artisan::call(sprintf('make:controller %s/%s --type=service-update --with-resource=%s/%s --model=%s', $pluralClass, $this->string($key), $singularClass, $singularClass, $singularClass));
        }

        Artisan::call(sprintf('make:controller %s/%s --type=service-paginated --with-resource=%s/%s', $pluralClass, $this->string('ListController'), $singularClass, $singularClass));
        Artisan::call(sprintf('make:controller %s/%s --type=service-view --with-resource=%s/%s --model=%s', $pluralClass, $this->string('ViewController'), $singularClass, $singularClass, $singularClass));
        Artisan::call(sprintf('make:controller %s/%s --type=service-delete --with-resource=%s/%s --model=%s', $pluralClass, $this->string('DeleteController'), $singularClass, $singularClass, $singularClass));


        // REQUESTS
        foreach(['DeleteRequest', 'UpdateRequest', 'ViewRequest'] as $key) {
            $gate = $this->string((string)Str::of($key)->replaceEnd('Request', '')->kebab());
            Artisan::call(sprintf('make:request %s/%s -p %s --type=key --route-model=%s', $pluralClass, $this->string($key), $gate, Str::of($singularClass)->camel()));
        }

        foreach(['CreateRequest', 'ListRequest'] as $key) {
            $gate = $this->string((string)Str::of($key)->replaceEnd('Request', '')->kebab());
            Artisan::call(sprintf('make:request %s/%s -p %s', $pluralClass, $this->string($key), $gate));
        }


        // RESOURCES
        Artisan::call(sprintf('make:resource %s/%s', $singularClass, $singularClass));
        Artisan::call(sprintf('make:resource %s/%sCollection', $singularClass, $singularClass));


        // POLICY
        Artisan::call(sprintf('make:policy %sPolicy --model=%s --method-list=%s --method-view=%s --method-create=%s --method-update=%s --method-delete=%s', $singularClass, $singularClass, $this->string('list'), $this->string('view'), $this->string('create'), $this->string('update'), $this->string('delete')));


        // OUTPUTS MANUAIS
        // @todo autoadd to file
        $this->alert('Routes: ' . base_path('routes/api.php'));
        $this->line(sprintf('Route::prefix(\'%s\')->middleware(\'auth:api\')->group(function() {', Str::of($pluralClass)->kebab()));
        $this->line("\t" . sprintf('Route::get(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('ListController')));
        $this->line("\t" . sprintf('Route::post(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('CreateController')));
        $this->line("\t" . sprintf('Route::patch(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('UpdateController')));
        $this->line("\t" . sprintf('Route::get(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('ViewController')));
        $this->line("\t" . sprintf('Route::delete(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', Str::of($singularClass)->camel(), $pluralClass, $this->string('DeleteController')));
        $this->line('});');

        // @todo autoadd to file
        $this->alert('AppServiceProvider: ' . app_path('Providers/AppServiceProvider.php'));
        $this->line(sprintf('\\App\\Repositories\\%s\\Contracts\\%sRepository::class => \\App\\Repositories\\%s\\%sRepository::class,', $singularClass, $singularClass, $singularClass, $singularClass));
    }

    private function string(string $key) {
        if(!isset(self::TRANSLATE[$this->option('locale')])) {
            return $key;
        }

        return self::TRANSLATE[$this->option('locale')][$key];
    }
}
