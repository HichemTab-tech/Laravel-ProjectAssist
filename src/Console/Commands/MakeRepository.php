<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $repositoryClassName = Str::studly(class_basename($name)) . 'Repository';
        $repositoryClassName = str_replace("RepositoryRepository", "Repository", $repositoryClassName);
        $repositorySubdirectory = rtrim(dirname($name), '/\\');

        $repositoryStub = File::get(app_path('Console/Commands/stubs/repository.stub'));



        $repositoryStub = str_replace('{{className}}', $repositoryClassName, $repositoryStub);
        $repositoryStub = str_replace('\{{subdirectory}}', ($repositorySubdirectory != "." ? "\\".$repositorySubdirectory : ""), $repositoryStub);

        $repositoryPath = app_path('Repositories/' . $repositorySubdirectory . '/' . $repositoryClassName . '.php');
        File::ensureDirectoryExists(dirname($repositoryPath));
        File::put($repositoryPath, $repositoryStub);

        $this->info('Repository created successfully.');
        return CommandAlias::SUCCESS;
    }
}