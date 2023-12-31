<?php /** @noinspection ALL */

namespace App\Console\Commands\LaravelProjectAssist;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDataClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:data-class {name} {--serializable=null} {--hidden=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a data class';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $serialize = $this->option('serializable') !== 'null';
        if ($this->option('hidden') !== 'null') {
            $hidden = explode(',', $this->option('hidden'));
        } else {
            $hidden = [];
        }
        $fieldsNamesAndTypes = $this->collectFields($hidden);
        $className = Str::studly(class_basename($name));
        $subdirectory = rtrim(dirname($name), '/\\');

        $this->generateDataClass($className, $subdirectory, $fieldsNamesAndTypes, $serialize, $hidden);
        $this->generateDataClassBuilder($className, $subdirectory, $fieldsNamesAndTypes);

        $this->info("Data class and builder generated successfully.");
    }

    protected function collectFields(&$hidden): array
    {
        $fields = [];
        do {
            $field = $this->ask("Add field (e.g., string,id)?");
            $fields[] = $field;
            if ($field == "/auto") {
                array_pop($fields);
                $fields = array_merge($fields, ["id", 'hashedId', "creationDate"]);
                $hidden[] = "id";
            }
            elseif (Str::startsWith($field, "/")) {
                $fields = $field;
                break;
            }
        } while ($field != "");
        if (is_array($fields)) {
            array_pop($fields);
        }
        else{
            $fields = substr($fields, 1);
            $fields = explode(",", $fields);
            $fields = array_map(function ($field){
                $field = trim($field);
                return str_replace(" ", ",", $field);
            }, $fields);
        }

        $fields = array_values(array_unique($fields));

        $fieldsNamesAndTypes = [];
        foreach ($fields as $field) {
            $fieldData = explode(",", $field);
            if (count($fieldData) == 1) {
                $fieldData = [$this->getAutoType($field), $fieldData[0]];
            }
            elseif (count($fieldData) != 2) {
                continue;
            }
            $fieldsNamesAndTypes[] = [
                'name' => trim($fieldData[1]),
                'type' => trim($fieldData[0]),
            ];
        }

        return $fieldsNamesAndTypes;
    }

    protected function generateDataClass(string $className, string $subdirectory, array $fields, bool $serialize, array $hidden): void
    {
        $template = File::get(app_path('Console/Commands/LaravelProjectAssist/stubs/dataClass.stub'));
        $constructor = $this->generateConstructorBuilder($fields);
        $uses = [];
        if (count(array_filter($fields, function ($value) {
                return $value['type'] == "DateTime";
            })) != 0) {
            $uses[] = "DateTime";
        }
        if ($serialize) {
            $uses[] = "JsonSerializable";
            $serializable = " implements JsonSerializable";
            $serializableGetter = "\n\n    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): array
    {
        return [";
            $hidden = array_values(array_unique($hidden));
            foreach ($fields as $field) {
                if (in_array($field['name'], $hidden)) continue;
                $serializableGetter .= "\n            '".$field['name']."' => \$this->".$field['name'].",";
            }
            $serializableGetter .= "\n        ];\n    }";
        }
        $replacements = [
            '{{use}}' => $this->getUses($uses),
            '{{class}}' => $className,
            '{{serializable}}' => $serializable??"",
            '{{serializableGetter}}' => $serializableGetter??"",
            '{{fields}}' => $this->generateFields($fields),
            '{{getters}}' => $this->generateGetters($fields),
            '\{{subdirectory}}' => ($subdirectory != "." ? "\\".$subdirectory : ""),
            '{{constructorDocs}}' => $constructor[0],
            '{{constructorParams}}' => $constructor[1],
            '{{constructorBody}}' => $constructor[2],
        ];
        $generatedClass = str_replace(array_keys($replacements), array_values($replacements), $template);

        $classPath = app_path('Data/'.$subdirectory.'/'.$className . '.php');
        File::ensureDirectoryExists(dirname($classPath));
        File::put($classPath, $generatedClass);
    }

    protected function generateFields(array $fields): string
    {
        $fieldsCode = '';
        foreach ($fields as $field) {
            $fieldsCode .= "    /**\n    * @var {$field['type']}".($field['type'] != 'bool' ? '|null' : '')." \${$field['name']}\n    */\n";
            if ($field['type'] != 'bool') {
                $field['type'] = '?'.$field['type'];
            }
            $fieldsCode .= "    private {$field['type']} \${$field['name']};\n\n";
        }
        $fieldsCode .= "\n\n";

        return $fieldsCode;
    }

    protected function generateGetters(array $fields): string
    {
        $fieldsCode = [];
        foreach ($fields as $field) {
            $m = "get";
            if ($field['type'] == 'bool') {
                $m = 'is';
            }
            $fieldsCode_ = "    /**\n    * @return {$field['type']}".($field['type'] != 'bool' ? '|null' : '')."\n    */\n";
            $suffix = '';
            if ($field['type'] == 'string') {
                $suffix = " ?? ''";
            }
            if ($field['type'] != 'bool') {
                $field['type'] = '?'.$field['type'];
            }
            $fieldsCode_ .= "    public function $m".ucfirst($field['name'])."(): {$field['type']}
    {
        return \$this->{$field['name']}{$suffix};
    }";
            $fieldsCode[] = $fieldsCode_;
        }
        return implode("\n\n", $fieldsCode);
    }

    protected function generateConstructorBuilder(array $fields): array
    {
        $constructorDocs = '';
        foreach ($fields as $field) {
            $suffix = '';
            if ($field['type'] != 'bool') {
                $suffix = "|null";
            }
            $constructorDocs .= "    * @param {$field['type']}{$suffix} \${$field['name']}\n";
        }
        $constructorParams = [];
        foreach ($fields as $field) {
            if ($field['type'] != 'bool') {
                $field['type'] = '?'.$field['type'];
            }
            $constructorParams[] = "{$field['type']} \${$field['name']}";
        }
        $constructorParams = implode(", ", $constructorParams);
        $constructorBody = '';
        foreach ($fields as $field) {
            $constructorBody .= "        \$this->{$field['name']} = \${$field['name']};\n";
        }
        return [$constructorDocs, $constructorParams, $constructorBody];
    }

    protected function generateDataClassBuilder(string $className, string $subdirectory, array $fields): void
    {
        $template = File::get(app_path('Console/Commands/LaravelProjectAssist/stubs/dataClassBuilder.stub'));
        $replacements = [
            '{{class}}' => $className,
            '{{builderFields}}' => $this->generateBuilderFields($fields),
            '{{builderMethods}}' => $this->generateBuilderMethods($fields),
            '{{builderParams}}' => $this->generateBuildMethodParams($fields),
            '\{{subdirectory}}' => ($subdirectory != "." ? "\\".$subdirectory : "")
        ];
        $generatedBuilder = str_replace(array_keys($replacements), array_values($replacements), $template);


        $classPath = app_path('Data/'.$subdirectory.'/'.$className . 'Builder.php');
        File::ensureDirectoryExists(dirname($classPath));
        File::put($classPath, $generatedBuilder);
    }


    protected function generateBuilderFields(array $fields): string
    {
        $fieldsCode = '';
        foreach ($fields as $field) {
            $fieldsCode .= "    /**\n    * @var {$field['type']}".($field['type'] != 'bool' ? '|null' : '')." \${$field['name']}\n    */\n";
            $suffix = "";
            if ($field['type'] == 'string') {
                $suffix = " = \"\"";
            }
            elseif ($field['type'] == 'bool') {
                $suffix = " = false";
            }
            elseif ($field['type'] == 'int' || $field['type'] == 'float') {
                $suffix = " = 0";
            }
            if ($field['type'] != 'bool') {
                $field['type'] = '?'.$field['type'];
            }
            $fieldsCode .= "    private {$field['type']} \${$field['name']}{$suffix};\n";
        }

        return $fieldsCode;
    }

    protected function generateBuildMethodParams(array $fields): string
    {
        return implode(", ", array_map(function ($field) { return "\$this->{$field['name']}";}, $fields));
    }

    protected function generateBuilderMethods(array $fields): string
    {
        $methodsCode = '';
        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'];
            $methodsCode .= "    /**\n    * @param {$field['type']}".($field['type'] != 'bool' ? '|null' : '')." \${$field['name']}\n    * @return self\n    */";
            if ($fieldType != 'bool') {
                $fieldType = '?'.$fieldType;
            }
            $methodsCode .= "
    public function with".ucfirst($fieldName)."($fieldType \$$fieldName): self
    {
        \$this->$fieldName = \$$fieldName;
        return \$this;
    }\n";
        }
        return $methodsCode;
    }

    private function getUses($uses) {
        $use = "";
        foreach ($uses as $usePath) {
            $use .= "use $usePath;\n";
        }
        if ($use != "") {
            $use = "\n\n".$use;
        }
        return $use;
    }

    private function getAutoType($field): string
    {
        if (Str::contains($field, 'date', true)) {
            return 'DateTime';
        }
        elseif (Str::contains($field, 'number', true)) {
            return 'int';
        }
        return "string";
    }
}