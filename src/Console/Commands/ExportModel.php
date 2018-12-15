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
    protected $signature = "trident:export:model {entity_name} {model?} {--only=} {--api} {--parent=} ";

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
            $model = $this->argument('model');
            $only = $this->option('only');
            $api = $this->option('api');
            $withArr = !empty($with) ? explode(",", $with) : [];
            $onlyArr = !empty($only) ? explode(",", $only) : '';
            $parent = $this->option('parent');

            $builders = new Export\Model($entity_name);

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

}