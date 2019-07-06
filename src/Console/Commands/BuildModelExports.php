<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Build;

class BuildModelExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:build:model_exports {--output-path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all model exports from current models';
    
    /**
     * @var Build\ModelExports
     */
    private $model_exports;

    public function __construct(Build\ModelExports $model_exports = null)
    {
        parent::__construct();

        $this->model_exports = new Build\ModelExports;
        if (!empty($model_exports)) {
            $this->model_exports = $model_exports;
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
            $input_path = $this->option('output-path');
            $output_path = $this->option('output-path');
            

            $crud = $this->model_exports->generate([
                'input_path' => $input_path,
                'output_path' => $output_path,
            ], $this);

            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
    

}