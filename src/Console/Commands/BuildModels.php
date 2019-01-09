<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Build;

class BuildModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:build:models {--output-path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all models from current database connection';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // $entity_name = $this->argument('entity_name');
            // $function_name = $this->argument('function_name');
            // $model = $this->argument('model');
            $output_path = !empty($this->option('output-path')) ? $this->option('output-path') : base_path().'/database/generated_models/';
            
            $this->makeDirectory($output_path);
            

            $crud = new Build\Models([
                'output_path' => $output_path,
            ], $this);

            
            // $this->info("\n".'nice! now add "use App\Trident\Workflows\Validations\\'.$validation_class_name.'Request;" on top of your "'.$controller_class_name.'" and you are ready to go.');
            
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