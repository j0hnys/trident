<?php

namespace App\Trident\Business\Logic;

use App\Trident\Business\Exceptions\PrinterException;
use App\Trident\Interfaces\Business\Logic\PrinterInterface;


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
    public function __construct()
    {
       //
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
        $string_parameter = $data['string_parameter'];
        $integer_parameter = $data['integer_parameter'];

        print_r([
            'string_parameter' => $data['string_parameter'],
            'integer_parameter' => $data['integer_parameter'],
        ]);

        if ($integer_parameter != 1) {
            throw new PrinterException('integer_parameter is not 1');
        } else {
            // $this->printer_repository
        }

        return $data;

    }

}
