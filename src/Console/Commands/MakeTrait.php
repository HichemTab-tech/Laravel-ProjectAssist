<?php

namespace HichemtabTech\LaravelProjectAssist\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

/**
 *
 */
class MakeTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $traitName = Str::studly(class_basename($name)) . 'Trait';
        $traitName = str_replace("TraitTrait", "Trait", $traitName);
        $traitSubdirectory = rtrim(dirname($name), '/\\');

        $traitStub = File::get(app_path('Console/Commands/stubs/trait.stub'));

        $traitStub = str_replace('{{className}}', $traitName, $traitStub);
        $traitStub = str_replace('\{{subdirectory}}', ($traitSubdirectory != "." ? "\\".$traitSubdirectory : ""), $traitStub);

        $traitPath = app_path('Traits/' . $traitSubdirectory . '/' . $traitName . '.php');
        File::ensureDirectoryExists(dirname($traitPath));
        File::put($traitPath, $traitStub);

        $this->info("Trait $traitName created successfully.");
        return CommandAlias::SUCCESS;
    }
}
