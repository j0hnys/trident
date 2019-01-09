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
    public function __construct($td_entity_name, $output_path)
    {
        
        
        $td_entity_name = ucfirst($td_entity_name);

        $app = new App();

        $model = $app->make('App\Models\\'.$td_entity_name);

        $db_table_name = $model->getTable();
        $db_table_fillables = $model->getFillable();

        $tmp = [];
        foreach ($db_table_fillables as $key => $db_table_fillable) {
            $tmp []= $this->schemaItem($db_table_fillable, \Schema::getColumnType($db_table_name, $db_table_fillable));
        }

        //
        //export
        $schema_export_path = $output_path.$td_entity_name.'.json';
        $this->makeDirectory($schema_export_path);

        file_put_contents($schema_export_path, json_encode($tmp,JSON_PRETTY_PRINT));
        

    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
    
    protected function schemaItem(string $column_name = '', string $column_type = ''): array
    {
        $item = [
            'column_name' => $column_name,
            'column_type' => $column_type,
            'type' => 'fillable',
        ];

        if ($column_type == 'string') {
            $item['validation_rules'] = [
                "required" => true,
                "type" => "string",
                "trigger" => "blur",
            ];
            $item['attributes'] = [
                "type" => ["string" => true],
                "default_value" => '\'\'',
                "element_type" => false,
            ];
        } else if ($column_type == 'integer') {
            $item['validation_rules'] = [
                "required" => true,
                "type" => "number",
                "trigger" => "blur",
            ];
            $item['attributes'] = [
                "type" => ["number" => true],
                "default_value" => '0',
                "element_type" => false,
            ];
        }

        return $item;
    }

}