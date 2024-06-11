<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class Model extends ModelMakeCommand
{
    protected function getStub()
    {
        if($this->option('with-collection')) {
            $this->resolveStubPath('/stubs/model.collection.stub');
        }
        return parent::getStub();
    }

    protected function resolveStubPath($stub)
    {
        if(file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))) {
            return $customPath;
        } else if (file_exists(__DIR__.$stub)) {
            return __DIR__.$stub;
        } else {
            return parent::resolveStubPath($stub);
        }
    }

    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['with-collection', null, InputOption::VALUE_OPTIONAL, 'Informs that model should have custom collection'];
        return $options;
    }
}