<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Support\Str;

class MakeSeeder extends SeederMakeCommand
{
	use Modularize {
		getPath as getModularPath;
	}

    protected function getPath($name)
    {
        $path = parent::getPath($name);

		if ($module = $this->option('module')){
            $name = last(explode('\\', $name));
            $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

            $path = $this->module()->path('src/database/seeders').'/'.$name.'.php';
        }

        return $path;
    }

	protected function replaceNamespace(&$stub, $name)
	{
		if ($module = $this->module()) {
			if (version_compare($this->getLaravel()->version(), '9.6.0', '<')) {
				$namespace = $module->qualify('src\\database\\seeders');
				$stub = str_replace('namespace src\\database\\seeders;', "namespace {$namespace};", $stub);
			}
		}

		return parent::replaceNamespace($stub, $name);
	}

	protected function rootNamespace()
	{
		if ($module = $this->module()) {
			if (version_compare($this->getLaravel()->version(), '9.6.0', '>=')) {
				return $module->qualify('src\\database\\seeders');
			}
		}

		return parent::rootNamespace();
	}
}
