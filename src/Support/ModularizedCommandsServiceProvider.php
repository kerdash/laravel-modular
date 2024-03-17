<?php

namespace HassanKerdash\Modular\Support;

use Livewire\Commands as Livewire;
use Illuminate\Console\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Application as Artisan;
use HassanKerdash\Modular\Console\Commands\Make\MakeJob;
use HassanKerdash\Modular\Console\Commands\Make\MakeCast;
use HassanKerdash\Modular\Console\Commands\Make\MakeEnum;
use HassanKerdash\Modular\Console\Commands\Make\MakeMail;
use HassanKerdash\Modular\Console\Commands\Make\MakeRule;
use HassanKerdash\Modular\Console\Commands\Make\MakeTest;
use HassanKerdash\Modular\Console\Commands\Make\MakeClass;
use HassanKerdash\Modular\Console\Commands\Make\MakeEvent;
use HassanKerdash\Modular\Console\Commands\Make\MakeModel;
use HassanKerdash\Modular\Console\Commands\Make\MakeTrait;
use HassanKerdash\Modular\Console\Commands\Make\MakePolicy;
use HassanKerdash\Modular\Console\Commands\Make\MakeSeeder;
use HassanKerdash\Modular\Console\Commands\Make\MakeChannel;
use HassanKerdash\Modular\Console\Commands\Make\MakeCommand;
use HassanKerdash\Modular\Console\Commands\Make\MakeFactory;
use HassanKerdash\Modular\Console\Commands\Make\MakeRequest;
use HassanKerdash\Modular\Console\Commands\Make\MakeListener;
use HassanKerdash\Modular\Console\Commands\Make\MakeLivewire;
use HassanKerdash\Modular\Console\Commands\Make\MakeObserver;
use HassanKerdash\Modular\Console\Commands\Make\MakeProvider;
use HassanKerdash\Modular\Console\Commands\Make\MakeResource;
use HassanKerdash\Modular\Console\Commands\Make\MakeComponent;
use HassanKerdash\Modular\Console\Commands\Make\MakeException;
use HassanKerdash\Modular\Console\Commands\Make\MakeInterface;
use HassanKerdash\Modular\Console\Commands\Make\MakeMigration;
use HassanKerdash\Modular\Console\Commands\Make\MakeController;
use HassanKerdash\Modular\Console\Commands\Make\MakeMiddleware;
use HassanKerdash\Modular\Console\Commands\Make\MakeNotification;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as OriginalMakeMigrationCommand;

class ModularizedCommandsServiceProvider extends ServiceProvider
{
	protected array $overrides = [
        'command.class.make' => MakeClass::class,
        'command.interface.make' => MakeInterface::class,
		'command.trait.make' => MakeTrait::class,
		'command.enum.make' => MakeEnum::class,
		'command.cast.make' => MakeCast::class,
		'command.controller.make' => MakeController::class,
		'command.console.make' => MakeCommand::class,
		'command.channel.make' => MakeChannel::class,
		'command.event.make' => MakeEvent::class,
		'command.exception.make' => MakeException::class,
		'command.factory.make' => MakeFactory::class,
		'command.job.make' => MakeJob::class,
		'command.listener.make' => MakeListener::class,
		'command.mail.make' => MakeMail::class,
		'command.middleware.make' => MakeMiddleware::class,
		'command.model.make' => MakeModel::class,
		'command.notification.make' => MakeNotification::class,
		'command.observer.make' => MakeObserver::class,
		'command.policy.make' => MakePolicy::class,
		'command.provider.make' => MakeProvider::class,
		'command.request.make' => MakeRequest::class,
		'command.resource.make' => MakeResource::class,
		'command.rule.make' => MakeRule::class,
		'command.seeder.make' => MakeSeeder::class,
		'command.test.make' => MakeTest::class,
		'command.component.make' => MakeComponent::class
	];

	public function register(): void
	{
		// Register our overrides via the "booted" event to ensure that we override
		// the default behavior regardless of which service provider happens to be
		// bootstrapped first (this mostly matters for Livewire).
		$this->app->booted(function() {
			Artisan::starting(function(Application $artisan) {
				$this->registerMakeCommandOverrides();
				$this->registerMigrationCommandOverrides();
				$this->registerLivewireOverrides($artisan);
			});
		});
	}

	protected function registerMakeCommandOverrides()
	{
		foreach ($this->overrides as $alias => $class_name) {
			$this->app->singleton($alias, $class_name);
			$this->app->singleton(get_parent_class($class_name), $class_name);
		}
	}

	protected function registerMigrationCommandOverrides()
	{
		// Laravel 8
		$this->app->singleton('command.migrate.make', function($app) {
			return new MakeMigration($app['migration.creator'], $app['composer']);
		});

		// Laravel 9
		$this->app->singleton(OriginalMakeMigrationCommand::class, function($app) {
			return new MakeMigration($app['migration.creator'], $app['composer']);
		});
	}

	protected function registerLivewireOverrides(Artisan $artisan)
	{
		// Don't register commands if Livewire isn't installed
		if (! class_exists(Livewire\MakeCommand::class)) {
			return;
		}

		// Replace the resolved command with our subclass
		$artisan->resolveCommands([MakeLivewire::class]);

		// Ensure that if 'make:livewire' or 'livewire:make' is resolved from the container
		// in the future, our subclass is used instead
		$this->app->extend(Livewire\MakeCommand::class, function() {
			return new MakeLivewire();
		});
		$this->app->extend(Livewire\MakeLivewireCommand::class, function() {
			return new MakeLivewire();
		});
	}
}
