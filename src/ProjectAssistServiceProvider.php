<?php

namespace HichemtabTech\LaravelProjectAssist;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProjectAssistServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish the repository files
        $this->publishes([
            __DIR__.'\Repositories' => app_path('Repositories'),
        ], 'laravel-assets');

        // Publish the console files
        $this->publishes([
            __DIR__.'\Console' => app_path('Console'),
        ], 'laravel-assets');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register any bindings or services here
    }
}