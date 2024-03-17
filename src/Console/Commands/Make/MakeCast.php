<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Foundation\Console\CastMakeCommand;

class MakeCast extends CastMakeCommand
{
	use Modularize;

    protected $prefix_path = "\\app";

    protected function getDefaultNamespace($rootNamespace)
	{
		if (!$this->option('module')) return parent::getDefaultNamespace($rootNamespace);

		if ($module = $this->module()) $rootNamespace = rtrim($module->namespaces->first(), '\\');

		return $rootNamespace.$this->prefix_path.'\Casts';
	}
}
