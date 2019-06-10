<?php

namespace j0hnys\Trident\Builders\Factories;


use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class Factory
{
    
    /**
     * laravel instance from command
     */
    protected $laravel;


    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($laravel, string $model = '')
    {
        
        $mustache = new \Mustache_Engine;
        $this->laravel = $laravel;

        if (empty($model)) {
            throw new \Exception("model name cannot be empty", 1);
        }

        //
        //
        // handle abstract classes, interfaces, ...
        $reflectionClass = new \ReflectionClass($model);

        if (!$reflectionClass->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
            throw new \Exception($model." is not a model", 1);
        }

        $fullpath_to_create = base_path().'/database/factories/models/'.$reflectionClass->getName().'php';
        if (file_exists($fullpath_to_create)) {
            throw new \Exception($fullpath_to_create . ' already exists!');
        }

        if (!$reflectionClass->IsInstantiable()) {
            // ignore abstract class or interface
            throw new \Exception($fullpath_to_create . ' cannot be instantiated');
        }


        $model_instance = $this->laravel->make($model);

        $properties = [];

        $properties_from_table = $this->getPropertiesFromTable($model_instance);

        $methods = $this->getPropertiesFromMethods($model_instance);

        $properties = array_values(array_merge($properties_from_table, $methods));

        $factory_path = base_path().'/database/factories/Models/'.$reflectionClass->getShortName().'.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/database/factories/Factory.stub');
        $stub = $mustache->render($stub, [
            'class_name' => get_class($model_instance),
            'properties' => $properties,
        ]);

        if (file_exists($factory_path)) {
            throw new \Exception($factory_path . ' already exists!');
        }
        $this->makeDirectory($factory_path);

        file_put_contents($factory_path, $stub);
    }


    /**
     * Load the properties from the database table.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    private function getPropertiesFromTable($model)
    {
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager($table);
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $properties = [];

        $platformName = $databasePlatform->getName();
        $customTypes = $this->laravel['config']->get("ide-helper.custom_db_types.{$platformName}", array());
        foreach ($customTypes as $yourTypeName => $doctrineTypeName) {
            $databasePlatform->registerDoctrineTypeMapping($yourTypeName, $doctrineTypeName);
        }

        $database = null;
        if (strpos($table, '.')) {
            list($database, $table) = explode('.', $table);
        }

        $columns = $schema->listTableColumns($table, $database);

        if ($columns) {
            foreach ($columns as $column) {
                $name = $column->getName();
                if (in_array($name, $model->getDates())) {
                    $type = 'datetime';
                } else {
                    $type = $column->getType()->getName();
                }
                if (!($model->incrementing && $model->getKeyName() === $name) &&
                    $name !== $model::CREATED_AT &&
                    $name !== $model::UPDATED_AT
                ) {
                    if(!method_exists($model,'getDeletedAtColumn') || (method_exists($model,'getDeletedAtColumn') && $name !== $model->getDeletedAtColumn())) {
                        $properties[$name] = $this->setProperty($name, $type);
                    }
                }
            }
        }


        return $properties;
    }


     /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function getPropertiesFromMethods($model)
    {
        $methods = get_class_methods($model);

        $properties = [];

        if ($methods) {
            foreach ($methods as $method) {
                if (!method_exists('Illuminate\Database\Eloquent\Model', $method) && !Str::startsWith($method, 'get')) {
                    //Use reflection to inspect the code, based on Illuminate/Support/SerializableClosure.php
                    $reflection = new \ReflectionMethod($model, $method);
                    $file = new \SplFileObject($reflection->getFileName());
                    $file->seek($reflection->getStartLine() - 1);
                    $code = '';
                    while ($file->key() < $reflection->getEndLine()) {
                        $code .= $file->current();
                        $file->next();
                    }
                    $code = trim(preg_replace('/\s\s+/', '', $code));
                    $begin = strpos($code, 'function(');
                    $code = substr($code, $begin, strrpos($code, '}') - $begin + 1);
                    foreach (array(
                                 'belongsTo',
                             ) as $relation) {
                        $search = '$this->' . $relation . '(';
                        if ($pos = stripos($code, $search)) {
                            //Resolve the relation's model to a Relation object.
                            $relationObj = $model->$method();
                            if ($relationObj instanceof Relation) {
                                $relatedModel = '\\' . get_class($relationObj->getRelated());
                                $relatedObj = new $relatedModel;

                                $property = $relatedObj->getForeignKey();
                                $properties[$property] = $this->setProperty($property,'function () {
             return factory('.get_class($relationObj->getRelated()).'::class)->create()->'.$relatedObj->getKeyName().';
        }');
                            }
                        }
                    }
                }
            }
        }


        return $properties;
    }


    /**
     * @param string $name
     * @param string|null $type
     */
    private function setProperty($name, $type = null)
    {

        $property = [];
        $property['column_name'] = $name;
        $property['type'] = 'mixed';
        $property['faker'] = false;
        
        if ($type !== null) {
            $property['type'] = $type;
        }

        if (Str::startsWith($type,'function ()')) {
            $property['faker'] = true;
        }

        $fakeableTypes = [
            'string' => '$faker->word',
            'text' => '$faker->text',
            'date' => '$faker->date()',
            'time' => '$faker->time()',
            'guid' => '$faker->word',
            'datetimetz' => '$faker->dateTimeBetween()',
            'datetime' => '$faker->dateTimeBetween()',
            'integer' => '$faker->randomNumber()',
            'bigint' => '$faker->randomNumber()',
            'smallint' => '$faker->randomNumber()',
            'decimal' => '$faker->randomFloat()',
            'float' => '$faker->randomFloat()',
            'boolean' => '$faker->boolean'
        ];

        $fakeableNames = [
            'name' => '$faker->name',
            'firstname' => '$faker->firstName',
            'first_name' => '$faker->firstName',
            'lastname' => '$faker->lastName',
            'last_name' => '$faker->lastName',
            'street' => '$faker->streetName',
            'zip' => '$faker->postcode',
            'postcode' => '$faker->postcode',
            'city' => '$faker->city',
            'country' => '$faker->country',
            'latitude' => '$faker->latitude',
            'lat' => '$faker->latitude',
            'longitude' => '$faker->longitude',
            'lng' => '$faker->longitude',
            'phone' => '$faker->phoneNumber',
            'phone_numer' => '$faker->phoneNumber',
            'company' => '$faker->company',
            'email' => '$faker->safeEmail',
            'username' => '$faker->userName',
            'user_name' => '$faker->userName',
            'password' => 'bcrypt($faker->password)',
            'url' => '$faker->url',
            'remember_token' => 'str_random(10)',
            'uuid' => '$faker->uuid',
            'guid' => '$faker->uuid',
        ];

        if (isset($fakeableNames[$name])) {
            $property['faker'] = true;
            $property['type'] = $fakeableNames[$name];
        }

        if (isset($fakeableTypes[$type]) && !$property['faker']) {
            $property['faker'] = true;
            $property['type'] = $fakeableTypes[$type];
        }


        return $property;
    }


    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    private function makeDirectory(string $path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }

     /**
     * make the appropriate file for the class if necessary.
     *
     * @param  string $path
     * @return void
     */
    private function makeFile(string $name, string $fullpath_to_create, string $stub_fullpath)
    {
        
        if (file_exists($fullpath_to_create)) {
            throw new \Exception($fullpath_to_create . ' already exists!');
        }

        $this->makeDirectory($fullpath_to_create);

        $stub = file_get_contents($stub_fullpath);

        $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        
        file_put_contents($fullpath_to_create, $stub);
    }
    

    /**
     * return the names of all events from trigger folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentWorkflows()
    {
        $files = scandir(base_path().'/app/Trident/Workflows/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * return the names of all events from subscriber folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentBusinesses()
    {
        $files = scandir(base_path().'/app/Trident/Business/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }


}