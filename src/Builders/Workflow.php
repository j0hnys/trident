<?php

namespace j0hnys\Trident\Builders;

class Workflow
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($name = 'TEST')
    {
        
        $mustache = new \Mustache_Engine;
        
        //
        //workflow logic generation
        $workflow_logic_path = base_path().'/app/Trident/Workflows/Logic/'.ucfirst(strtolower($name)).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/Logic.stub';
        $this->makeFile(
            $name,
            $workflow_logic_path,
            $stub_path
        );


        //
        //workflow exception generation
        $workflow_exception_path = base_path().'/app/Trident/Workflows/Exceptions/'.ucfirst(strtolower($name)).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicException.stub';
        $this->makeFile(
            $name,
            $workflow_exception_path,
            $stub_path
        );


        //
        //workflow interface generation
        $workflow_interface_path = base_path().'/app/Trident/Interfaces/Workflows/Logic/'.ucfirst(strtolower($name)).'Interface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicInterface.stub';
        $this->makeFile(
            $name,
            $workflow_interface_path,
            $stub_path
        );

        //
        //workflow repository generation
        $workflow_repository_path = base_path().'/app/Trident/Workflows/Repositories/'.ucfirst(strtolower($name)).'Repository.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicRepository.stub';
        $this->makeFile(
            $name,
            $workflow_repository_path,
            $stub_path
        );

        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //business logic generation
        $business_logic_path = base_path().'/app/Trident/Business/Logic/'.ucfirst(strtolower($name)).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/Logic.stub';
        $this->makeFile(
            $name,
            $business_logic_path,
            $stub_path
        );

        //
        //business logic exception generation
        $business_logic_exception_path = base_path().'/app/Trident/Business/Exceptions/'.ucfirst(strtolower($name)).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicException.stub';
        $this->makeFile(
            $name,
            $business_logic_exception_path,
            $stub_path
        );

        //
        //business logic interface generation
        $business_logic_interface_path = base_path().'/app/Trident/Interfaces/Business/Logic/'.ucfirst(strtolower($name)).'Interface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicInterface.stub';
        $this->makeFile(
            $name,
            $business_logic_interface_path,
            $stub_path
        );


        //
        //update TridentServiceProvider
        $Td_entities_workflows = $this->getCurrentWorkflows();
        $Td_entities_businesses = $this->getCurrentBusinesses();

        $workflows = array_map(function($element){
            return [
                'Td_entity' => $element,
            ];
        },$Td_entities_workflows);

        $businesses = array_map(function($element){
            return [
                'Td_entity' => $element,
            ];
        },$Td_entities_businesses);


        $trident_event_service_provider_path = base_path().'/app/Providers/TridentServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../src/Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
        ]);

        file_put_contents($trident_event_service_provider_path, $stub);



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

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
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


    /**
     * Get code and save to disk
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        //
    }

}