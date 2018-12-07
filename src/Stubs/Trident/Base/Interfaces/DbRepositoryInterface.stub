<?php

namespace App\Trident\Base\Interfaces;

interface DbRepositoryInterface
{
    public function all();
    
    public function save();

    public function show($id);

    public function paginate(int $perPage, array $columns);
    
    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function findBy($attribute, $value, array $columns);

}