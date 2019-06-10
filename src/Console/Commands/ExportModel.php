<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Export;

class ExportModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:export:model {entity_name} {--output-path=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'export a models schema';
    
    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Export\Model();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $entity_name = $this->argument('entity_name');
            $output_path = $this->option('output-path');
            
            $builders = $this->model->generate($entity_name, $output_path);

            $this->info($entity_name.' model\'s export successful!');            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}