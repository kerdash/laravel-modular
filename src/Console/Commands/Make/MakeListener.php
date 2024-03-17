<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\ListenerMakeCommand;
use HassanKerdash\Modular\Support\Facades\Modules;

class MakeListener extends ListenerMakeCommand
{
	use Modularize;

    protected $prefix_path = "\\app";

    protected function getDefaultNamespace($rootNamespace)
	{
		if (!$this->option('module')) return parent::getDefaultNamespace($rootNamespace);

		if ($module = $this->module()) $rootNamespace = rtrim($module->namespaces->first(), '\\');

		return $rootNamespace.$this->prefix_path.DIRECTORY_SEPARATOR.'Listeners';
	}

	protected function buildClass($name)
	{
		$event = $this->option('event');

		if (Modules::moduleForClass($name)) {
			$stub = str_replace(
				['DummyEvent', '{{ event }}'],
				class_basename($event),
				GeneratorCommand::buildClass($name)
			);

			return str_replace(
				['DummyFullEvent', '{{ eventNamespace }}'],
				trim($event, '\\'),
				$stub
			);
		}

		return parent::buildClass($name);
	}
}
