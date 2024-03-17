<?php

namespace HassanKerdash\Modular\Console\Commands\Make;

use Illuminate\Foundation\Console\TestMakeCommand;
use Illuminate\Support\Str;

class MakeTest extends TestMakeCommand
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

            $path = $this->module()->path('src/tests').'/'.$name.'.php';
        }

        return $path;
    }

	protected function rootNamespace()
	{
		if ($module = $this->module()) {
			return $module->namespaces->first().'Tests';
		}

		return 'Tests';
	}
}
