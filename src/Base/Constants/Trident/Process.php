<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Definitions\Definition;

final class Process extends Definition
{
    const schema = [
        "used_interfaces" => [
            [
                "interface" => "T::string()"            //__interface__\\__interface__\\__interface__
            ]
        ],
        "constructor_parameters" => [
            [
                "type" => "T::string()",                //Type
                "name" => "T::string()"                 //parameter
            ]
        ],
        "process_steps" => [
            [
                "step_name" => "T::string()",           //step
                "step_function_parameters" => [
                    [
                        "type" => "T::string()",        //Type
                        "name" => "T::string()"         //parameter
                    ]
                ],
                "step_returned_type" => "T::string()",  //ReturnType
                "step_code" =>  "",
                "step_return_value" => "T::string()"    //new ReturnType();
            ]
        ]
    ];
}

