<?php

namespace j0hnys\Trident\Builders\Export;

use Illuminate\Container\Container as App;

class Model
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_name)
    {
        
        $td_entity_name = ucfirst($td_entity_name);

        $app = new App();

        $model = $app->make('App\Models\\'.$td_entity_name);

        $db_table_name = $model->getTable();
        $db_table_fillables = $model->getFillable();

        $tmp = [];
        foreach ($db_table_fillables as $key => $db_table_fillable) {
            $tmp []= [
                'column_name' => $db_table_fillable,
                'column_type' => \Schema::getColumnType($db_table_name, $db_table_fillable),
                'type' => 'fillable',
            ];    
        }

        //
        //export
        $schema_export_path = base_path().'/app/Models/Schemas/Exports/'.$td_entity_name.'.json';
        $this->makeDirectory($schema_export_path);

        file_put_contents($schema_export_path, json_encode($tmp,JSON_PRETTY_PRINT));
        

    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
    
    /**
     * Get code and save to disk
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        //
    }

}