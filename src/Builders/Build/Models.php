<?php

namespace j0hnys\Trident\Builders\Build;

use Illuminate\Console\Command;

class Models
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($data = [], Command $command)
    {
        $output_path = $data['output_path'];
        
        $table_names = \DB::connection()->getDoctrineSchemaManager()->listTableNames();
        
        foreach ($table_names as $table_name) {
            $camel_case_table_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
            $camel_case_table_name[0] = strtoupper($camel_case_table_name[0]);

            $command->call('krlove:generate:model', [
                'class-name' => $camel_case_table_name,
                '--output-path' => $output_path,
                '--table-name' => $table_name,
                '--namespace' => 'App\\Models'
            ]);
        }
    

    }
    

}