<?php

namespace AndreGumieri\LaravelCrud\Console\Commands\Concerns;

use Symfony\Component\Console\Input\InputOption;

/**
 * Registers the extra options of a command straight on its input definition.
 *
 * Laravel 13 rewrote several core "make:" commands from the $name + getOptions()
 * style to $signature, and Illuminate\Console\Command only calls getOptions()
 * when the command has no $signature. Overriding getOptions() on a subclass of
 * one of those commands therefore silently stops adding anything. Declaring the
 * options from configure() works on every supported Laravel version, because
 * Symfony calls it while the definition is still being built.
 */
trait RegistersExtraOptions
{
    protected function configure(): void
    {
        parent::configure();

        foreach ($this->extraOptions() as [$name, $shortcut, $mode, $description]) {
            if (! $this->getDefinition()->hasOption($name)) {
                $this->addOption($name, $shortcut, $mode, $description);
            }
        }
    }

    /**
     * The options this command adds on top of the ones it inherits.
     *
     * @return array<int, array{0: string, 1: string|null, 2: int, 3: string}>
     */
    abstract protected function extraOptions(): array;
}
