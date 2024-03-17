<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;

class MakeFactory extends FactoryMakeCommand
{
	use Modularize;

    protected function getPath($name)
    {
        $path = parent::getPath($name);

		if ($module = $this->option('module')){
            $name = last(explode('\\', $name));
            $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish('Factory');

            $path = $this->module()->path('src/database/factories').'/'.str_replace('\\', '/', $name).'.php';
        }

        return $path;
    }

	protected function replaceNamespace(&$stub, $name)
	{
		if ($module = $this->module()) {
			$model = $this->option('model')
				? $this->qualifyModel($this->option('model'))
				: $this->qualifyModel($this->guessModelName($name));

			$models_namespace = $module->qualify('Models');

			if (Str::startsWith($model, "{$models_namespace}\\")) {
				$extra_namespace = trim(Str::after(Str::beforeLast($model, '\\'), $models_namespace), '\\');
				$namespace = rtrim($module->qualify("database\\factories\\{$extra_namespace}"), '\\');
			} else {
				$namespace = $module->qualify('database\\factories');
			}

			$replacements = [
				'{{ factoryNamespace }}' => $namespace,
				'{{factoryNamespace}}' => $namespace,
				'namespace database\factories;' => "namespace {$namespace};", // Early Laravel 8 didn't use a placeholder
			];

			$stub = str_replace(array_keys($replacements), array_values($replacements), $stub);
		}

		return parent::replaceNamespace($stub, $name);
	}

	protected function guessModelName($name)
	{
		if ($module = $this->module()) {
			if (Str::endsWith($name, 'Factory')) {
				$name = substr($name, 0, -7);
			}

			$modelName = $this->qualifyModel($name);
			if (class_exists($modelName)) {
				return $modelName;
			}

			return $module->qualify('Models\\Model');
		}

		return parent::guessModelName($name);
	}
}
