<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeLangFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lang {name=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a lang file ressource';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        if ($name === 'null') {
            $name = $this->ask("file name?");
        }
        $scriptName = Str::studly(class_basename($name));
        $scriptName = lcfirst($scriptName);
        $subdirectory = rtrim(dirname($name), '/\\');

        $contents = $this->generatesLanguageContents();
        $dirs = array_keys($contents);
        foreach ($dirs as $dir) {
            $replacements = [
                '{{CONTENT}}' => $contents[$dir],
            ];
            $template = File::get(app_path('Console/Commands/stubs/lang.stub'));
            $generatedClass = str_replace(array_keys($replacements), array_values($replacements), $template);

            $classPath = lang_path($dir.'/'.$subdirectory.'/'.$scriptName . '.php');
            File::ensureDirectoryExists(dirname($classPath));
            File::put($classPath, $generatedClass);
        }



        $this->info("Lang files generated successfully.");
    }

    private function generatesLanguageContents() :array
    {
        $alpha = 'abcd';
        $dirs = File::directories(lang_path());
        $dirs = array_map(function ($d) {return basename($d);}, $dirs);
        $dirs = array_filter($dirs, function ($d) {return !in_array($d, ['.', '..']);});
        $contents = [];
        foreach ($dirs as $dir) {
            if ($dir == 'fr') {
                $contentKey = 'Exemple';
            }
            elseif ($dir == 'en') {
                $contentKey = 'Example';
            }
            else{
                $contentKey = 'Ex';
            }
            for ($i = 0; $i < 3; $i++) {
                $content = "\n        '".$alpha[$i]."' => '".$contentKey." 1-".$alpha[$i]."',";
            }
            $contents[$dir] = $content;
        }
        return $contents;
    }
}