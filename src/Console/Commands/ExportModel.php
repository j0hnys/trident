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
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $entity_name = $this->argument('entity_name');
            $output_path = !empty($this->option('output-path')) ? $this->option('output-path') : base_path().'/app/Models/Schemas/Exports/';

            $this->makeDirectory($output_path);
            
            $builders = new Export\Model($entity_name, $output_path);

            $this->info($entity_name.' model\'s export successful!');

            
            // //new validation class
            // $this->call('trident:generate:validation', [
            //     'entity_name' => $entity_name,
            //     'function_name' => $function_name,
            // ]);

            
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