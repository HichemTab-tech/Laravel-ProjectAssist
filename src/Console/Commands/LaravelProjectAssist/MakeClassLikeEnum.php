<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Console\Commands\LaravelProjectAssist;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeClassLikeEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:class-like-enum {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a data class file like enum but with int values to each field';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $className = Str::studly(class_basename($name));
        $subdirectory = rtrim(dirname($name), '/\\');

        $onlyValues = $this->ask('Do you want the fields to have a label or you want them only by values? (y/n)', 'n') == "n";
        $prefix = $this->ask('Do you want to add a prefix to the fields? (e.g., "TYPE1 TYPE2" becomes "PREFIX_TYPE1 PREFIX_TYPE2") leave it blank if you dont', '');
        if ($onlyValues) {
            $fields = $this->collectFields($prefix);
        } else {
            $fields = $this->collectFieldsWithLabels($prefix);
        }

        $defaultStartingNumber = 1;
        do {
            $defaultStartingNumber *= 10;
        } while (count($fields) >= $defaultStartingNumber);




        $startingNumber = $this->ask('What number do you want to start the values from?', $defaultStartingNumber);

        while (!$this->checkStartingNumber($startingNumber, $fields)) {
            $this->error("The number you entered is too small, please enter a number greater like " . $defaultStartingNumber . " or more (add a zero)");
            $startingNumber = $this->ask('What number do you want to start the values from?', $defaultStartingNumber);
        }

        $replaceFields = "";
        foreach ($fields as $key => $field) {
            $f = $onlyValues ? $field : $key;
            $replaceFields .= "\n    const " . $f . " = " . ($startingNumber) . ";";
            $startingNumber++;
        }


        $replaceInAllMethod = "";
        if ($onlyValues) {
            for ($i = 0; $i < count($fields); $i++) {
                $replaceInAllMethod .= "\n            self::" . $fields[$i] . ",";
            }
        } else {
            foreach ($fields as $key => $value) {
                $value = str_replace("'", "\\'", $value);
                $replaceInAllMethod .= "\n            self::" . $key . " => '" . $value . "',";
            }
        }

        $replacements = [
            '{{class}}' => $className,
            '\{{subdirectory}}' => ($subdirectory != "." ? "\\".$subdirectory : ""),
            '{{fields}}' => $replaceFields,
            '{{values}}' => $replaceInAllMethod,
        ];

        $extended = (!$onlyValues) ? "classLikeEnumKeyAndValue" : "classLikeEnumValueOnly";

        $template = File::get(app_path('Console/Commands/LaravelProjectAssist/stubs/'.$extended.'.stub'));
        $generatedClass = str_replace(array_keys($replacements), array_values($replacements), $template);

        $classPath = app_path('Enums/'.$subdirectory.'/'.$className . '.php');
        File::ensureDirectoryExists(dirname($classPath));
        File::put($classPath, $generatedClass);

        $this->info("Class generated successfully!");
    }

    function checkStartingNumber($startingNumber, $fields): bool
    {
        if ($startingNumber <= count($fields)) {
            return false;
        }
        $str = strval($startingNumber);
        $n = intval((intval($str[0])+1).str_repeat("0", strlen($str) - 1));
        return ($n - $startingNumber) > count($fields);
    }

    protected function collectFields($prefix): array
    {
        $fields = $this->ask("Add fields separated by white space (e.g., TYPE1 TYPE2...)?");
        $fields = trim($fields);
        $fields = explode(" ", $fields);
        $fields = array_map(function ($field) use ($prefix) {
            $field = trim($field);
            if ($prefix != "") {
                $field = $prefix . "_" . $field;
            }
            $field = strtoupper($field);
            return preg_replace("/[^a-zA-Z0-9_\x7f-\xff]/", "", $field);
        }, $fields);
        $fields = array_filter($fields, function ($field) {
            return $field != "";
        });
        return array_values(array_unique($fields));
    }

    protected function collectFieldsWithLabels($prefix): array
    {
        $fields = [];
        do {
            $field = $this->ask("Add field with label separated by ,,(e.g., first type,,TYPE1)?");
            $field = trim($field);
            $fields[] = $field;
            if (Str::startsWith($field, "/")) {
                $fields = $field;
                break;
            }
        } while ($field != "");
        if (is_array($fields)) {
            array_pop($fields);
        }
        else{
            $fields = substr($fields, 1);
            $fields = explode("//", $fields);
            $fields = array_map('trim', $fields);
        }
        $fields_ = array_filter($fields, function ($field) {
            return $field != "" AND Str::contains($field, ",,");
        });
        $fields_2 = [];
        for ($i = 0; $i < count($fields_); $i++) {
            $parts = explode(",,", $fields_[$i]);
            $fields_2[$parts[1]] = $parts[0];
        }

        $keys = array_keys($fields_2);
        $keys = array_values(array_unique($keys));
        $fields = [];
        for ($i = 0; $i < count($keys); $i++) {
            $fields[$keys[$i]] = $fields_2[$keys[$i]];
        }

        return array_combine(
            array_map(function ($key) use ($prefix) {
                $key = trim($key);
                if ($prefix != "") {
                    $key = $prefix . "_" . $key;
                }
                $key = strtoupper($key);
                return preg_replace("/[^a-zA-Z0-9_\x7f-\xff]/", "", $key);
            }, array_keys($fields)),
            array_map('trim', array_values($fields))
        );
    }
}