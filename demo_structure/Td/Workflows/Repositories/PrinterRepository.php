<?php 

namespace App\Td\Workflows\Repositories;

use App\Td\Base\Repositories\DbRepository;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class PrinterRepository extends DbRepository
{
    use WorkflowTrait;
    
    // needed to select appropriate data source
    public function model()
    {
        return 'App\Printer';
    }

}