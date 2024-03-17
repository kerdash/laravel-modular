<?php

namespace StubModuleNamespace\StubClassNamePrefix\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:StubClassName:install';

    /**
     * Hide this from the console list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $composer_file = File::json(dirname(__DIR__, 4).DIRECTORY_SEPARATOR.'composer.json');

        $packages = array_merge(['modules/StubClassName' => '*'] ?? [], $composer_file['require'] ?? [], $composer_file['require-dev'] ?? []);

        $command = "composer require";
        foreach ($packages as $package => $version)
            $command .= " $package:$version";

        return exec($command);
    }
}
