<?php

namespace StubModuleNamespace\StubClassNamePrefix\app\Providers;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    protected $module = 'StubClassName';

    public function register()
    {
        //
    }

    public function boot()
    {
        // Configuration
        $configDir = dirname(__DIR__, 2) . '/config/';
        if (is_dir($configDir)) {
            foreach (Finder::create()->files()->name('*.php')->depth(0)->in($configDir) as $file) {
                $this->mergeConfigFrom($file->getRealPath(), $this->module . '::' . $file->getFilenameWithoutExtension());
            }
        }

        $publicConfigDir = dirname(__DIR__, 2) . '/config/public/';
        if (is_dir($publicConfigDir)) {
            foreach (Finder::create()->files()->name('*.php')->depth(0)->in($publicConfigDir) as $file) {
                $this->mergeConfigFrom($file->getRealPath(), $file->getFilenameWithoutExtension());
            }
        }

        // Migrations
        $migrationsDir = dirname(__DIR__, 2) . '/database/migrations';
        if (is_dir($migrationsDir)) {
            $this->loadMigrationsFrom($migrationsDir);
        }

        // Seeders
        $databaseSeederClass = 'Modules\\'.ucfirst($this->module).'\\database\\seeders\\DatabaseSeeder';
        if (class_exists($databaseSeederClass)) {
            $this->callAfterResolving(DatabaseSeeder::class, function ($seeder) use ($databaseSeederClass) { $seeder->call([$databaseSeederClass]);});
        }

        // Translations
        $resourcesDir = dirname(__DIR__, 2) . '/resources/';
        $langDir = $resourcesDir . 'lang/';
        if (is_dir($langDir)) {
            foreach (Finder::create()->directories()->name('lang')->in($resourcesDir) as $directory) {
                $this->loadTranslationsFrom($directory, $this->module);
            }
            foreach (Finder::create()->directories()->name('public')->in($langDir) as $directory) {
                foreach (File::directories($directory) as $langPath) {
                    foreach (File::allFiles($langPath) as $file) {
                        $array = include $file->getPathname();
                        $result = array_combine(array_map(function ($key) use ($file) {
                            return $file->getFilenameWithoutExtension() . '.' . $key;
                        }, array_keys($array)), array_values($array));
                        Lang::addLines($result, basename($langPath));
                    }
                }
            }
        }

        // Views
        $viewsDir = $resourcesDir . 'views/';
        if (is_dir($viewsDir)) {
            foreach (Finder::create()->directories()->name('views')->in($resourcesDir) as $directory) {
                $this->loadViewsFrom($directory, $this->module);
            }
        }

        // Commands
        $commandsDir = dirname(__DIR__) . '/Console/Commands/';
        if (is_dir($commandsDir)) {
            foreach (Finder::create()->files()->name('*.php')->depth(0)->in($commandsDir) as $file) {
                $this->commands(['Modules\\' . ucfirst($this->module) . '\\app\\Console\\Commands\\' . $file->getFilenameWithoutExtension()]);
            }
        }

        // Routes
        $routesDir = dirname(__DIR__, 2) . '/routes/';
        $routesFiles = ['web.php', 'api.php'];
        foreach ($routesFiles as $file) {
            $routeFile = $routesDir . $file;
            if (file_exists($routeFile)) {
                $this->loadRoutesFrom($routeFile);
            }
        }
    }
}
