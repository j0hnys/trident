<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:process {td_entity_name} {process_name} {schema_path} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a process';

    /**
     * @var Builders\Process
     */
    private $process;

    public function __construct(Builders\Process $process = null)
    {
        parent::__construct();

        $this->process = new Builders\Process();
        if (!empty($process)) {
            $this->process = $process;
        }
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $td_entity_name = $this->argument('td_entity_name');
            $process_name = $this->argument('process_name');
            $schema_path = $this->argument('schema_path');
            
            $this->process->generate($td_entity_name, $process_name, $schema_path);
            
            $this->info('process: "'.$process_name.'" made sucessfully');

        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}