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
        
        
        //
        //workflow logic generation
        $workflow_logic_path = base_path().'/app/Trident/Workflows/Logic/'.ucfirst(strtolower($name)).'.php';
        
        if (file_exists($workflow_logic_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' workflow logic already exists!');
        }

        $this->makeDirectory($workflow_logic_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Workflows/Logic.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($workflow_logic_path, $stub);


        //
        //workflow exception generation
        $workflow_exception_path = base_path().'/app/Trident/Workflows/Exceptions/'.ucfirst(strtolower($name)).'Exception.php';
        
        if (file_exists($workflow_exception_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' workflow exception already exists!');
        }

        $this->makeDirectory($workflow_exception_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Workflows/LogicException.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($workflow_exception_path, $stub);


        //
        //workflow interface generation
        $workflow_interface_path = base_path().'/app/Trident/Interfaces/Workflows/Logic/'.ucfirst(strtolower($name)).'Interface.php';
        
        if (file_exists($workflow_interface_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' workflow interface already exists!');
        }

        $this->makeDirectory($workflow_interface_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Workflows/LogicInterface.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($workflow_interface_path, $stub);


        //
        //workflow repository generation
        $workflow_repository_path = base_path().'/app/Trident/Workflows/Repositories/'.ucfirst(strtolower($name)).'Repository.php';
        
        if (file_exists($workflow_repository_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' workflow repository already exists!');
        }

        $this->makeDirectory($workflow_repository_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Workflows/LogicRepository.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($workflow_repository_path, $stub);

        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //business logic generation
        $business_logic_path = base_path().'/app/Trident/Business/Logic/'.ucfirst(strtolower($name)).'.php';
        
        if (file_exists($business_logic_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' business logic already exists!');
        }

        $this->makeDirectory($business_logic_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Business/Logic.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($business_logic_path, $stub);


        //
        //business logic exception generation
        $business_logic_exception_path = base_path().'/app/Trident/Business/Exceptions/'.ucfirst(strtolower($name)).'Exception.php';
        
        if (file_exists($business_logic_exception_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' business logic exception already exists!');
        }

        $this->makeDirectory($business_logic_exception_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Business/LogicException.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($business_logic_exception_path, $stub);


        //
        //business logic interface generation
        $business_logic_interface_path = base_path().'/app/Trident/Interfaces/Business/Logic/'.ucfirst(strtolower($name)).'Interface.php';
        
        if (file_exists($business_logic_interface_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' business logic interface already exists!');
        }

        $this->makeDirectory($business_logic_interface_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Business/LogicInterface.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
        
        file_put_contents($business_logic_interface_path, $stub);

        

    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
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