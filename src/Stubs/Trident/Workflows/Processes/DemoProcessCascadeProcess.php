<?php

namespace App\Trident\Workflows\Processes;

class DemoProcessCascadeProcess //<-- PROSOXH!! na ginei kai interface edw!!!
{

    public function __construct(array $something_DIed = [])	//<--PROSOXH!! THA MPEI MESA STO DIBIND!!!
    {
        // code
    }

    
    public function step_1(array $data): array 
    {
        // code  

        dump([
            'step_1'
        ]);
        
        return ['ena'];
    }

    public function step_2(array $data): array
    {
        // code 

        dump([
            'step_2'
        ]);
        
        return ['dyo'];
    }

    public function step_3(array $data): array
    {
        // code

        dump([
            'step_3'
        ]);

        return ['tria'];
    }

}
