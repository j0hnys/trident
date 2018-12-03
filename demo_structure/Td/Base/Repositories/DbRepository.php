<?php

namespace App\Td\Base\Repositories;

use App\Td\Base\Interfaces\DbRepositoryInterface;
use App\Td\Base\Exceptions\DbRepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
 
/**
 * Class Repository
 * @package Bosnadev\Repositories\Eloquent
 */
abstract class DbRepository implements DbRepositoryInterface {
 
    /**
     * @var App
     */
    private $app;
 
    /**
     * @var
     */
    protected $model;
 
    /**
     * Specify Model class name
     * 
     * @return mixed
     */
    abstract function model();

    /**
     * @param App $app
     * @throws App\Td\Base\Exceptions\DbRepositoryException
     */
    public function __construct(App $app) {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return Model
     * @throws RepositoryException
     */
    public function makeModel() {
        $model = $this->app->make($this->model());
 
        if (!$model instanceof Model)
            throw new DbRepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
 
        return $this->model = $model;
    }


    /**
     * This method breaks isolation from the Data Source implementation. It is used now to have all the features of
     * Eloquent and if there is a change that DB layer should be changed you will just have to come to this class and
     * re-implement all the functions from Eloquent that have been used in the app
     * 
     * @return Model
     */
    public function __call($method,$arguments) {
        if(method_exists($this, $method)) {
            return call_user_func_array(array($this,$method),$arguments);
        } else {
            return call_user_func_array(array($this->model,$method),$arguments);
        }
    }


    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }
    
    // save a new record in the database
    public function save()
    {
        return $this->model->save();
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model-findOrFail($id);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*')) {
        return $this->model->paginate($perPage, $columns);
    }
 
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data) {
        return $this->model->create($data);
    }
 
    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute="id") {
        return $this->model->where($attribute, '=', $id)->update($data);
    }
 
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id) {
        return $this->model->destroy($id);
    }
 
    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*')) {
        return $this->model->find($id, $columns);
    }
 
    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*')) {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }



    // set value
    public function __set($name, $value)
    {
        return $this->model->{$name} = $value;
    }

    // get value
    public function __get($name)
    {
        return $this->model->{$name};
    }


}