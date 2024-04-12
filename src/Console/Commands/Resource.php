<?php

namespace AndreGumieri\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Illuminate\Support\Str;

class Resource extends ResourceMakeCommand
{
    protected function buildClass($name)
    {
        $replaces = [];
        if($this->collection()) {
            $replaces = ['{{collects}}' => preg_replace('/Collection$/', '', class_basename($name))];
        }

        return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
    }
}
