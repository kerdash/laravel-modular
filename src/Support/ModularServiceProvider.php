<?php

namespace HassanKerdash\Modular\Support;

use ReflectionClass;
use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use HassanKerdash\Modular\Console\Commands\ModulesList;
use HassanKerdash\Modular\Console\Commands\ModulesSync;
use HassanKerdash\Modular\Console\Commands\ModulesCache;
use HassanKerdash\Modular\Console\Commands\ModulesClear;
use HassanKerdash\Modular\Console\Commands\ModulesInstall;
use HassanKerdash\Modular\Console\Commands\Make\MakeModule;
use HassanKerdash\Modular\Console\Commands\Make\MakeMigration;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

class ModularServiceProvider extends ServiceProvider
{
	protected ?ModuleRegistry $registry = null;

	protected ?AutoDiscoveryHelper $auto_discovery_helper = null;

	protected string $base_dir;

	protected ?string $modules_path = null;

	public function __construct($app)
	{
		parent::__construct($app);

		$this->base_dir = str_replace('\\', '/', dirname(__DIR__, 2));
	}

	public function register(): void
	{
		$this->mergeConfigFrom("{$this->base_dir}/config.php", 'app-modules');

		$this->app->singleton(ModuleRegistry::class, function() {
			return new ModuleRegistry(
				$this->getModulesBasePath(),
				$this->app->bootstrapPath('cache/modules.php')
			);
		});

		$this->app->singleton(AutoDiscoveryHelper::class);

		$this->app->singleton(MakeMigration::class, function($app) {
			return new MigrateMakeCommand($app['migration.creator'], $app['composer']);
		});
	}

	public function boot(): void
	{
		$this->publishVendorFiles();
		$this->bootPackageCommands();
	}

	protected function registry(): ModuleRegistry
	{
		return $this->registry ??= $this->app->make(ModuleRegistry::class);
	}

	protected function autoDiscoveryHelper(): AutoDiscoveryHelper
	{
		return $this->auto_discovery_helper ??= $this->app->make(AutoDiscoveryHelper::class);
	}

	protected function publishVendorFiles(): void
	{
		$this->publishes([
			"{$this->base_dir}/config.php" => $this->app->configPath('app-modules.php'),
		], 'modular-config');
	}

	protected function bootPackageCommands(): void
	{
		if (! $this->app->runningInConsole())
			return;

		$this->commands([
            MakeModule::class,
			ModulesInstall::class,
			ModulesCache::class,
			ModulesClear::class,
			ModulesSync::class,
			ModulesList::class,
		]);
    }

	protected function getModulesBasePath(): string
	{
		if (null === $this->modules_path) {
			$directory_name = $this->app->make('config')->get('app-modules.modules_directory', 'app-modules');
			$this->modules_path = str_replace('\\', '/', $this->app->basePath($directory_name));
		}

		return $this->modules_path;
	}

	protected function isInstantiableCommand($command): bool
	{
		return is_subclass_of($command, Command::class)
			&& ! (new ReflectionClass($command))->isAbstract();
	}
}
