<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MakeController extends ControllerMakeCommand
{
	use Modularize;

    protected $prefix_path = "\\app";

	protected function parseModel($model)
	{
		if (! $module = $this->module())
			return parent::parseModel($model);

		if (preg_match('([^A-Za-z0-9_/\\\\])', $model))
			throw new InvalidArgumentException('Model name contains invalid characters.');

		$model = trim(str_replace('/', '\\', $model), '\\');

		if (! Str::startsWith($model, $namespace = $module->namespaces->first()))
			$model = $namespace.$model;

		return $model;
	}

    protected function getDefaultNamespace($rootNamespace)
	{
		if (!$this->option('module')) return parent::getDefaultNamespace($rootNamespace);

		$namespace = parent::getDefaultNamespace($rootNamespace);
		$module = $this->module();

		if ($module && false === strpos($rootNamespace, $module->namespaces->first())) {
			$find = rtrim($rootNamespace, '\\');
			$replace = rtrim($module->namespaces->first(), '\\') . $this?->prefix_path;
			$namespace = str_replace($find, $replace, $namespace);
		}

		return $namespace;
	}
}
