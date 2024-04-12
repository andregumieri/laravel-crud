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
    protected $signature = 'make:crud {singular} {plural}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria controller, service, repository, model, migration, policy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $singularClass = $this->argument('singular');
        $pluralClass = $this->argument('plural');
        $singularString = (string)Str::of($singularClass)->lower();
        $pluralString = (string)Str::of($pluralClass)->lower();

        Artisan::call('make:model ' . $singularClass . ' -m');

        Artisan::call('make:repository ' . $singularClass);

        // @todo Alterar o stub para já chamar o repository fazendo a ação que deve fazer
        Artisan::call('make:service ' . $pluralClass . '/CriarService');
        Artisan::call('make:service ' . $pluralClass . '/DeletarService');
        Artisan::call('make:service ' . $pluralClass . '/AlterarService');
        Artisan::call('make:service ' . $pluralClass . '/VerService');
        Artisan::call('make:service ' . $pluralClass . '/ListarService');

        // @todo Alterar o stub para já chamar a service
        Artisan::call('make:controller ' . $pluralClass . '/CriarController' . ' --type=service');
        Artisan::call('make:controller ' . $pluralClass . '/DeletarController' . ' --type=service');
        Artisan::call('make:controller ' . $pluralClass . '/AlterarController' . ' --type=service');
        Artisan::call('make:controller ' . $pluralClass . '/VerController' . ' --type=service');
        Artisan::call('make:controller ' . $pluralClass . '/ListarController' . ' --type=service');

        // @todo Alterar o stub para que o gate já esteja preenchido
        Artisan::call('make:request ' . $pluralClass . '/CriarRequest');
        Artisan::call('make:request ' . $pluralClass . '/DeletarRequest');
        Artisan::call('make:request ' . $pluralClass . '/AlterarRequest');
        Artisan::call('make:request ' . $pluralClass . '/VerRequest');
        Artisan::call('make:request ' . $pluralClass . '/ListarRequest');

        Artisan::call(sprintf('make:resource %s/%s', $singularClass, $singularClass));
        Artisan::call(sprintf('make:resource %s/%sCollection', $singularClass, $singularClass));

        Artisan::call('make:policy ' . $singularClass . 'Policy --model=' . $singularClass);

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
        $this->line("\t" . sprintf('Route::get(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, 'ListarController'));
        $this->line("\t" . sprintf('Route::post(\'/\', \App\Http\Controllers\%s\%s::class);', $pluralClass, 'CriarController'));
        $this->line("\t" . sprintf('Route::put(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, 'AlterarController'));
        $this->line("\t" . sprintf('Route::get(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, 'VerController'));
        $this->line("\t" . sprintf('Route::delete(\'/{%s}\', \App\Http\Controllers\%s\%s::class);', $singularString, $pluralClass, 'DeletarController'));
        $this->line('});');

    }
}
