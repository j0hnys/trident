<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use Illuminate\Console\Command;

class Process
{
    private $storage_disk;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
    }


    public function generate(string $td_entity_name, string $process_name, string $schema_path, Command $command)
    {
        $this->generateProcess($td_entity_name, $process_name, $schema_path);
        $this->generateOther(
            $process_name,
            'app/Trident/Workflows/Processes/'.$td_entity_name.'',
            'app/Trident/Interfaces/Workflows/Processes/'.$td_entity_name.'',
            $command
        );
    }

    
    /**
     * @param string $td_entity_name
     * @param boolean $is_collection
     * @param string $domain
     * @return void
     */
    public function generateProcess(string $td_entity_name, string $process_name, string $schema_path): void
    {
        //Resource logic generation
        $process_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Processes/'.$td_entity_name.'/'.$process_name.'.php';
        
        if ($this->storage_disk->fileExists($process_path)) {
            throw new \Exception(ucfirst($td_entity_name) . $process_name . ' already exists!');
        }


        $schema = [];
        if (!empty($schema_path)) {
            $schema = json_decode( $this->storage_disk->readFile( $schema_path ), true);
        }

        $template_data = [];
        if (!empty($schema)) {

            $template_data['used_interfaces'] = $schema['used_interfaces'];
            $template_data['process_name'] = $process_name;
            
            
            $template_data['constructor_parameters'] = array_map(function($element) {
                return $element['type'].' $'.$element['name'];
            },$schema['constructor_parameters']);
            $template_data['constructor_parameters'] = implode(', ', $template_data['constructor_parameters']);

            $template_data['constructor_body'] = array_map(function($element) {
                return '$this->'.$element['name'].' = $'.$element['name'].';';
            },$schema['constructor_parameters']);
            $template_data['constructor_body'] = implode("\r\n        ", $template_data['constructor_body']);


            $template_data['process_steps'] = $schema['process_steps'];

            foreach ($template_data['process_steps'] as $i => $process_step) {
                $process_step['step_function_parameters'] = array_map(function($element) {
                    return $element['type'].' $'.$element['name'];
                },$process_step['step_function_parameters']);
                $process_step['step_function_parameters'] = implode(', ', $process_step['step_function_parameters']);

                $template_data['process_steps'][$i] = $process_step;
            }
        }

        $this->storage_disk->makeDirectory($process_path);        
        
        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/Workflows/Processes/CascadeProcess.stub');
        $stub = $this->mustache->render($stub, $template_data);

        $this->storage_disk->writeFile($process_path, $stub);
    }


    public function generateOther(string $process_name, string $relative_input_path, string $relative_output_path, Command $command)
    {
        $command->call('trident:refresh:class_interface', [
            'name' => $process_name,
            'relative_input_path' => $relative_input_path,  //'app/Trident/Workflows/Logic',
            'relative_output_path' => $relative_output_path,    //'app/Trident/Interfaces/Workflows/Logic',
        ]);
    }
    

}