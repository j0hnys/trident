<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Declarations;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Utilities\WordCaseConverter;

class Events
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;
    private $declarations;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->mustache = new \Mustache_Engine;
        $this->declarations = new Declarations();
        $this->folder_structure = new FolderStructure();
        $this->word_case_converter = new WordCaseConverter();
    }
    
    /**
     * @param string $td_entity_type
     * @param string $event_type
     * @param string $td_entity_name
     * @return void
     */
    public function generate(string $td_entity_type, string $event_type, string $td_entity_name): void
    {
        
        $td_entity_type = strtolower($td_entity_type);
        $event_type = strtolower($event_type);
        $td_entity_name = ucfirst($td_entity_name);
        

        $type = '';
        if ($td_entity_type == $this->declarations::ENTITIES['WORKFLOW']['name']) {
            $type = 'Workflows';
        } elseif ($td_entity_type == $this->declarations::ENTITIES['BUSINESS']['name']) {
            $type = 'Business';
            throw new \Exception('entity type '.$type.' is not supported at the moment');
        } else {
            throw new \Exception('entity type '.$type.' does not exists!');
        }

        if ($event_type != $this->declarations::EVENTS['TRIGGER_LISTENER']['name'] && $event_type != $this->declarations::EVENTS['SUBSCRIBER']['name']) {
            throw new \Exception('entity type '.$event_type.' does not exists!');
        }


        if ($event_type == $this->declarations::EVENTS['TRIGGER_LISTENER']['name']) {
            //
            //workflow trigger generation
            $this->folder_structure->checkPath('app/Trident/'.$type.'/Events/Triggers/*');
            $workflow_event_path = $this->storage_disk->getBasePath().'/app/Trident/'.$type.'/Events/Triggers/'.$td_entity_name.'Trigger.php';
            
            if (!$this->storage_disk->fileExists($workflow_event_path)) {
                
                $this->storage_disk->makeDirectory($workflow_event_path);

                $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$type.'/Events/LogicTrigger.stub');
                $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($td_entity_name), $stub);
                $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
                
                $this->storage_disk->writeFile($workflow_event_path, $stub);
            }

            //
            //workflow listener generation
            $this->folder_structure->checkPath('app/Trident/'.$type.'/Events/Listeners/*');
            $workflow_event_path = $this->storage_disk->getBasePath().'/app/Trident/'.$type.'/Events/Listeners/'.$td_entity_name.'Listener.php';
            
            if (!$this->storage_disk->fileExists($workflow_event_path)) {
                
                $this->storage_disk->makeDirectory($workflow_event_path);

                $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$type.'/Events/LogicListener.stub');
                $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($td_entity_name), $stub);
                $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
                
                $this->storage_disk->writeFile($workflow_event_path, $stub);
            }

        } elseif ($event_type == $this->declarations::EVENTS['SUBSCRIBER']['name']) {
            //
            //workflow subscriber generation
            $this->folder_structure->checkPath('app/Trident/'.$type.'/Events/Subscribers/*');
            $workflow_event_path = $this->storage_disk->getBasePath().'/app/Trident/'.$type.'/Events/Subscribers/'.$td_entity_name.ucfirst($event_type).'.php';
            
            if (!$this->storage_disk->fileExists($workflow_event_path)) {
                
                $this->storage_disk->makeDirectory($workflow_event_path);
                
                $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$type.'/Events/Logic'.ucfirst($event_type).'.stub');
                $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($td_entity_name), $stub);
                $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
                
                $this->storage_disk->writeFile($workflow_event_path, $stub);
            }

        }
        
        //
        //update TridentEventServiceProvider
        $Td_entities = $this->storage_trident->getCurrentEvents('Workflows');
        $Td_entities_subscribers = $this->storage_trident->getCurrentSubscribers('Workflows');

        $events = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
            ];
        },$Td_entities);

        $subscribers = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
            ];
        },$Td_entities_subscribers);


        $this->folder_structure->checkPath('app/Providers/TridentEventServiceProvider.php');
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentEventServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/app/Providers/TridentEventServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_triggers_events' => $events,
            'register_workflow_subscribers' => $subscribers,
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);


    }


}