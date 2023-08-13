<?php

namespace App\Console\Commands\LaravelProjectAssist;

use Illuminate\Console\Command;

class GenerateEnvExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a .env example from actual .env file and hide the important fields with #env_hide';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $lines = file(base_path().'/.env');
        $content = '';
        foreach ($lines as $line) {
            if(preg_match("/#.*env_hide/", $line)) {
                $line = preg_replace("/^([^=]+)=([^#]*\S)(\s*)#([^#]*)/", "$1=*****$3#$4", $line);
            }
            $content .= $line;
        }
        file_put_contents(base_path().'/.env.example', $content);

        $this->info("File .env.example generated successfully.");
    }
}