<?php

namespace Westery\Repository\Console;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ResourceControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:resource-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/controller.resource.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }else{
            throw new InvalidArgumentException('--model ModelClass is required');
        }

        if ($this->option('repository')) {
            $replace = $this->buildRepositoryReplacement($replace);
        }else{
            throw new InvalidArgumentException('--repository= RepositoryClass is required');
        }


        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }



    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Build the repository replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRepositoryReplacement(array $replace)
    {
        $repositoryClass = $this->parseRepository($this->option('repository'));
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($repositoryClass)) {
            if ($this->confirm("A {$repositoryClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:repository', ['name' => $repositoryClass,'--model'=>$modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullRepositoryClass' => $repositoryClass,
            'DummyRepositoryClass' => class_basename($repositoryClass),
            'DummyRepositoryVariable' => lcfirst(class_basename($repositoryClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $repository
     * @return string
     */
    protected function parseRepository($repository)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $repository)) {
            throw new InvalidArgumentException('Repository name contains invalid characters.');
        }

        $repository = trim(str_replace('/', '\\', $repository), '\\');

        if (! Str::startsWith($repository, $rootNamespace = $this->laravel->getNamespace())) {
            $repository = $rootNamespace.'Repositories\\'.$repository;
        }

        return $repository;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Generate a resource controller for the given model.'],
            ['repository', 'r', InputOption::VALUE_REQUIRED, 'Generate a resource controller class.'],
        ];
    }
}
