<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Foundation\Console\ResourceMakeCommand;

class MakeResource extends ResourceMakeCommand
{
	use Modularize;

    protected $prefix_path = "\\app\\Http";

    protected function getDefaultNamespace($rootNamespace)
	{
		if (!$this->option('module')) return parent::getDefaultNamespace($rootNamespace);

		if ($module = $this->module()) $rootNamespace = rtrim($module->namespaces->first(), '\\');

		return $rootNamespace.$this->prefix_path.DIRECTORY_SEPARATOR.'Resources';
	}
}
