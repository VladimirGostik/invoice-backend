<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // register all repositories and interfaces in the app/Repositories directory
        $path = app_path('Repositories');
        $files = File::files($path);

        foreach ($files as $file) {

            $name = $file->getBasename('.php');

            $this->app->bind(
                'App\Repositories\Interfaces\\'.$name.'Interface',
                'App\Repositories\\'.$name,
            );
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
