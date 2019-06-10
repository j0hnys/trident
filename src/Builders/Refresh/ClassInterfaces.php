<?php

namespace j0hnys\Trident\Builders\Refresh;


class ClassInterfaces
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($command, $type)
    {

        if ($type == 'workflows') {
            $workflow_names = $this->getCurrentWorkflows();

            foreach ($workflow_names as $workflow_name) {
                $command->call('trident:refresh:class_interface', [
                    'name' => $workflow_name,
                    'relative_input_path' => 'app/Trident/Workflows/Logic',
                    'relative_output_path' => 'app/Trident/Interfaces/Workflows/Logic',
                ]);
            }
        } else if ($type == 'businesses') {
            $business_names = $this->getCurrentBusinesses();

            foreach ($business_names as $business_name) {
                $command->call('trident:refresh:class_interface', [
                    'name' => $business_name,
                    'relative_input_path' => 'app/Trident/Business/Logic',
                    'relative_output_path' => 'app/Trident/Interfaces/Business/Logic',
                ]);
            }
        }


    }



    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory(string $path)
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
    protected function makeFile(string $name, string $fullpath_to_create, string $stub_fullpath)
    {
        
        if (file_exists($fullpath_to_create)) {
            // throw new \Exception($fullpath_to_create . ' already exists!');
            return;
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
