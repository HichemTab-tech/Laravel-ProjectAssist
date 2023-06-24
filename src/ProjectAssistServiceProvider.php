<?php

namespace HichemtabTech\LaravelProjectAssist;

use Illuminate\Support\ServiceProvider;

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
            __DIR__.'/src/Repositories' => app_path('Repositories'),
        ], 'project-assist-repositories');

        // Publish the console files
        $this->publishes([
            __DIR__.'/src/Console' => app_path('Console'),
        ], 'project-assist-console');
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