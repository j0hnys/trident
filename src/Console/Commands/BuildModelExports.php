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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $input_path = !empty($this->option('output-path')) ? $this->option('output-path') : base_path().'/app/Models/';
            $output_path = !empty($this->option('output-path')) ? $this->option('output-path') : base_path().'/database/generated_model_exports/';
            
            $this->makeDirectory($output_path);
            

            $crud = new Build\ModelExports([
                'input_path' => $input_path,
                'output_path' => $output_path,
            ], $this);

            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }


     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!is_dir(($path))) {
            mkdir(($path), 0777, true);
        }
    }

}