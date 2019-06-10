<?php

namespace j0hnys\Trident\Builders\Export;

use Illuminate\Container\Container as App;
use j0hnys\Trident\Base\Storage\Disk;

class Model
{
    private $app;
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->app = new App();
    }

    public function generate(string $td_entity_name, ?string $output_path): void
    {   
        $td_entity_name = ucfirst($td_entity_name);
        $output_path = !empty($output_path) ? $output_path : $this->storage_disk->getBasePath().'/app/Models/Schemas/Exports/';

        $this->storage_disk->makeDirectory($output_path);

        $model = $this->app->make('App\Models\\'.$td_entity_name);

        $db_table_name = $model->getTable();
        $db_table_fillables = $model->getFillable();

        $tmp = [];
        foreach ($db_table_fillables as $key => $db_table_fillable) {
            $tmp []= $this->schemaItem($db_table_fillable, \Schema::getColumnType($db_table_name, $db_table_fillable));
        }

        //
        //export
        $schema_export_path = $output_path.$td_entity_name.'.json';
        $this->storage_disk->makeDirectory($schema_export_path);

        $this->storage_disk->writeFile($schema_export_path, json_encode($tmp,JSON_PRETTY_PRINT));       

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