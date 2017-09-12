<?php

namespace Westery\Repository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->commands('Westery\Repository\Console\RepositoryMakeCommand');
        $this->commands('Westery\Repository\Console\ResourceControllerMakeCommand');

    }
}
