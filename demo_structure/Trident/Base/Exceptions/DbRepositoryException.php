<?php

namespace App\Trident\Base\Exceptions;

use Exception;

class DbRepositoryException extends Exception
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
        return response('DbRepositoryException!!');

        // return parent::render($request);
    }
}