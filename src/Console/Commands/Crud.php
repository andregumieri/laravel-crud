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

        Artisan::call('make:model ' . $singularClass . ' -m');

        Artisan::call('make:repository ' . $singularClass);

        // @todo Alterar o stub para já chamar o repository fazendo a ação que deve fazer
        foreach(['CreateService', 'DeleteService', 'UpdateService', 'ViewService', 'ListService'] as $key) {
            Artisan::call(sprintf('make:service %s/%s', $pluralClass, $this->string($key)));
        }

        // @todo Alterar o stub para já chamar a service
        foreach(['CreateController', 'DeleteController', 'UpdateController', 'ViewController', 'ListController'] as $key) {
            Artisan::call(sprintf('make:controller %s/%s --type=service --with-resource=%s/%s', $pluralClass, $this->string($key), $singularClass, $singularClass));
        }

        foreach(['CreateRequest', 'DeleteRequest', 'UpdateRequest', 'ViewRequest', 'ListRequest'] as $key) {
            Artisan::call(sprintf('make:request %s/%s', $pluralClass, $this->string($key)));
        }

        Artisan::call(sprintf('make:resource %s/%s', $singularClass, $singularClass));
        Artisan::call(sprintf('make:resource %s/%sCollection', $singularClass, $singularClass));

        Artisan::call(sprintf('make:policy %sPolicy --model=%s', $singularClass, $singularClass));

        $this->alert('Gates: ' . app_path('Providers/AuthServiceProvider.php'));
        $this->line(sprintf('Gate::define(\'%s-view-any\', [%sPolicy::class, \'viewAny\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-view\', [%sPolicy::class, \'view\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-create\', [%sPolicy::class, \'create\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-update\', [%sPolicy::class, \'update\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-delete\', [%sPolicy::class, \'delete\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-restore\', [%sPolicy::class, \'restore\']);', $singularString, $singularClass));
        $this->line(sprintf('Gate::define(\'%s-force-delete\', [%sPolicy::class, \'forceDelete\']);', $singularString, $singularClass));

        $this->alert('Routes: ' . base_path('routes/api.php'));
        $this->line(sprintf('Route::prefix(\'%s\')->middleware(\'auth:api\')->group(function() {', $pluralString));
        $this->line("\t" . sprintf('Route::get(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('ListController')));
        $this->line("\t" . sprintf('Route::post(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, $this->string('CreateController')));
        $this->line("\t" . sprintf('Route::put(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, $this->string('UpdateController')));
        $this->line("\t" . sprintf('Route::get(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, $this->string('ViewController')));
        $this->line("\t" . sprintf('Route::delete(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, $this->string('DeleteController')));
        $this->line('});');
    }

    private function string($key) {
        if(!isset(self::TRANSLATE[$this->option('locale')])) {
            return $key;
        }

        return self::TRANSLATE[$this->option('locale')][$key];
    }
}
