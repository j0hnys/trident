<?php

namespace j0hnys\Trident\Builders\Refresh;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Declarations;

class ClassInterfaces
{
    private $storage_disk;
    private $storage_trident;
    private $declarations;

    public function __construct()
    {
        $this->storage_disk = new Disk();        
        $this->storage_trident = new Trident();
        $this->declarations = new Declarations();
    }

    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function run($command, $type)
    {

        if ($type == $this->declarations::ENTITIES['WORKFLOW']['name']) {
            $workflow_names = $this->storage_trident->getCurrentWorkflows();

            foreach ($workflow_names as $workflow_name) {
                $command->call('trident:refresh:class_interface', [
                    'name' => $workflow_name,
                    'relative_input_path' => 'app/Trident/Workflows/Logic',
                    'relative_output_path' => 'app/Trident/Interfaces/Workflows/Logic',
                ]);
            }
        } else if ($type == $this->declarations::ENTITIES['BUSINESS']['name']) {
            $business_names = $this->storage_trident->getCurrentBusinesses();

            foreach ($business_names as $business_name) {
                $command->call('trident:refresh:class_interface', [
                    'name' => $business_name,
                    'relative_input_path' => 'app/Trident/Business/Logic',
                    'relative_output_path' => 'app/Trident/Interfaces/Business/Logic',
                ]);
            }
        }


    }



}
