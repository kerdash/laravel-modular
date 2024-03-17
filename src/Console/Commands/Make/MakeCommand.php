<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Illuminate\Support\Str;

class MakeCommand extends ConsoleMakeCommand
{
	use Modularize;

    protected $prefix_path = "\\app";

    protected function getDefaultNamespace($rootNamespace)
	{
		if (!$this->option('module')) return parent::getDefaultNamespace($rootNamespace);

		if ($module = $this->module()) $rootNamespace = rtrim($module->namespaces->first(), '\\');

		return $rootNamespace.$this->prefix_path.DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR.'Commands';
	}
}
