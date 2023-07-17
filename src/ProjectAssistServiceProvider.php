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

        $this->addNamespaceToPublishedFiles();
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

    /**
     * Add the namespace to the published console files.
     *
     * @return void
     */
    protected function addNamespaceToPublishedFiles(): void
    {
        $files = File::allFiles(app_path('Console'));

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $namespace = 'App\Console\Commands';

            if (!Str::contains($contents, 'namespace '.$namespace)) {
                $updatedContents = str_replace('<?php', "<?php\n\nnamespace {$namespace};", $contents);
                file_put_contents($file, $updatedContents);
            }
        }
        $files = File::allFiles(app_path('Repositories'));

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $namespace = 'App\Repositories';

            if (!Str::contains($contents, 'namespace '.$namespace)) {
                $updatedContents = str_replace('<?php', "<?php\n\nnamespace {$namespace};", $contents);
                file_put_contents($file, $updatedContents);
            }
        }
    }
}