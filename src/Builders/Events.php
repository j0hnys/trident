<?php

namespace j0hnys\Trident\Builders;

class Events
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_type, $event_type, $td_entity_name)
    {
        
        $td_entity_type = strtolower($td_entity_type);
        $event_type = strtolower($event_type);
        $td_entity_name = ucfirst(strtolower($td_entity_name));
        
        $mustache = new \Mustache_Engine;

        $type = '';
        if ($td_entity_type == 'workflow') {
            $type = 'Workflows';
        } else if ($td_entity_type == 'business') {
            $type = 'Business';
            throw new \Exception('entity type '.$type.' is not supported at the moment');
        } else {
            throw new \Exception('entity type '.$type.' does not exists!');
        }

        if ($event_type != 'trigger_listener' && $event_type != 'subscriber') {
            throw new \Exception('entity type '.$event_type.' does not exists!');
        }


        if ($event_type == 'trigger_listener') {
            //
            //workflow trigger generation
            $workflow_event_path = base_path().'/app/Trident/'.$type.'/Events/Triggers/'.$td_entity_name.'Trigger.php';
            
            if (file_exists($workflow_event_path)) {
                throw new \Exception(ucfirst(strtolower($td_entity_name)) . ' trigger already exists!');
            }

            $this->makeDirectory($workflow_event_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/'.$type.'/Events/LogicTrigger.stub');
            $stub = str_replace('{{td_entity}}', strtolower($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            
            file_put_contents($workflow_event_path, $stub);

            //
            //workflow listener generation
            $workflow_event_path = base_path().'/app/Trident/'.$type.'/Events/Listeners/'.$td_entity_name.'Listener.php';
            
            if (file_exists($workflow_event_path)) {
                throw new \Exception(ucfirst(strtolower($td_entity_name)) . ' listener already exists!');
            }

            $this->makeDirectory($workflow_event_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/'.$type.'/Events/LogicListener.stub');
            $stub = str_replace('{{td_entity}}', strtolower($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            
            file_put_contents($workflow_event_path, $stub);

        } else if ($event_type == 'subscriber') {
            //
            //workflow subscriber generation
            $workflow_event_path = base_path().'/app/Trident/'.$type.'/Events/'.$event_type.'/'.$td_entity_name.ucfirst($event_type_).'.php';
            
            if (file_exists($workflow_event_path)) {
                throw new \Exception(ucfirst(strtolower($td_entity_name)) . ' event already exists!');
            }
            
            $this->makeDirectory($workflow_event_path);
            
            $stub = file_get_contents(__DIR__.'/../../src/Stubs/'.$type.'/Events/Logic'.ucfirst($event_type_).'.stub');
            $stub = str_replace('{{td_entity}}', strtolower($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            
            file_put_contents($workflow_event_path, $stub);

        }
        
        //
        //update TridentEventServiceProvider
        $trident_event_service_provider_path = base_path().'/app/Providers/TridentEventServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../src/Stubs/app/Providers/TridentEventServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflow_triggers_events' => [
                'Td_entity' => 'Super_test'
            ]
        ]);

        file_put_contents($trident_event_service_provider_path, $stub);

        

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