<?php

namespace App\Trident\Workflows\Logic;

use App\Trident\Workflows\Exceptions\PrinterException;
use App\Trident\Workflows\Repositories\PrinterRepository;
use App\Trident\Interfaces\Workflows\Logic\PrinterInterface;
use App\Trident\Business\Logic;

class Printer implements PrinterInterface
{
    
    /**
     * @var App
     */
    protected $app;

    /**
     * @var PrinterRepository
     */
    protected $printer_repository;

    /**
     * constructor.
     *
     * @var string
     * @return void
     */
    public function __construct($app)
    {
        $this->printer_repository = new PrinterRepository($app);
        $this->printer = new Logic\Printer();
    }


    /**
     * test
     *
     * @return void
     */
    public function test()  //~not to be included in code generator~
    {
        echo 'in service';
    }

    /**
     * validate.
     *
     * @var array
     * @return void
     */
    public function another_function(Array $data)   //~not to be included in code generator~   //edw tha valw spatie Struct!!! (h similar)
    {
        $result = $this->printer->another_function([
            'string_parameter' => $data['string_parameter'],
            'integer_parameter' => $data['integer_parameter'],
        ]);

        
        $this->printer_repository->string_parameter = $result['string_parameter'];
        $this->printer_repository->integer_parameter = $result['integer_parameter'];
        $this->printer_repository->save();

        

        return true;

    }

    public function get_all()   //~not to be included in code generator~
    {
        return $this->printer_repository->all();
    }
}
