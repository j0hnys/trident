<?php

namespace j0hnys\Trident\Builders\Refresh;

use Illuminate\Console\Command;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Declarations;

class ClassInterfaces
{
    private $storage_disk;
    private $storage_trident;
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
        $this->declarations = new Declarations();
    }

    /**
     * @param Command $command
     * @param string $type
     * @return void
     */
    public function run(Command $command, string $type): void
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
        } elseif ($type == $this->declarations::ENTITIES['BUSINESS']['name']) {
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
