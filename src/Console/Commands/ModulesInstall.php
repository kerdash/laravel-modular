<?php

namespace HassanKerdash\Modular\Console\Commands;

use Nwidart\Modules\Json;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use HassanKerdash\Modular\Support\ModuleConfig;
use Symfony\Component\Console\Input\InputOption;
use HassanKerdash\Modular\Support\ModuleRegistry;
use Symfony\Component\Console\Input\InputArgument;

class ModulesInstall extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'modules:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install modules.';

    /**
     * Execute the console command.
     */
    public function handle(ModuleRegistry $registry)
    {
        $packages = [];

        foreach ($registry->modules() as $module) {
            $composer_file = File::json($module->base_path.DIRECTORY_SEPARATOR.'composer.json');
            $module = ['modules/'.$module->name => $module->name] ?? [];
            $require = $composer_file['require'] ?? [];
            $require_dev = $composer_file['require-dev'] ?? [];

            $packages = array_merge($packages, $module, $require, $require_dev);
        }

        return exec("composer require ".implode(" ", array_keys($packages)));
    }
}
