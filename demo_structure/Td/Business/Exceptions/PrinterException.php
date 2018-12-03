<?php

namespace App\Td\Business\Exceptions;

use Exception;

class PrinterException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {   
        // return parent::report($exception);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response('erlkmgfrelkfmgrelkfmrlemlkf');

        // return parent::render($request);
    }
}