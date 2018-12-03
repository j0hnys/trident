<?php
/**
 * Tuhin Bepari <digitaldreams40@gmail.com>
 */
namespace j0hnys\Trident\Builders\Crud;

use Illuminate\Support\Facades\Storage;

class Controller
{
    /**
     * Controller Name prefix.
     * If Model Name is User and no controller name is supplier then it will be User and then Controller will be appended.
     * Its name will be UserController
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * Sub Path of the Controller.
     * Generally Controller are stored in Controllers folder. But for grouping Controller may be put into folders.
     * @var type
     */
    public $path = '';
    
    /**
     * @var string
     */
    protected $template;

    protected $files;

    /**
     * @var array
     */
    protected $only = ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'];
    /**
     * @var bool|string
     */
    protected $parentModel;
    /**
     * ControllerCrud constructor.
     * @param $model
     * @param string $name
     * @param array|string $only
     * @param bool $api
     * @param bool|\Illuminate\Database\Eloquent\Model $parent
     * @internal param array $except
     * @throws \Exception
     */
    public function __construct($name = 'TEST')
    {
        
        $this->files = Storage::disk('local');

        $path = base_path().'/app/Http/Controllers/'.$name.'.php';
        
        if (file_exists($path)) {
            return $this->error($name . ' already exists!');
        }

        $this->makeDirectory($path);

        // $stub = $this->files->get(__DIR__ . '/../../Stubs/Crud/Controller.stub');
        $stub = file_get_contents(base_path() . '/app/Trident/Stubs/Crud/Controller.stub');

        $stub = str_replace('{{td_entity}}', $name, $stub);
        
        file_put_contents($path, $stub);

        // $this->info('Controller created successfully.');

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