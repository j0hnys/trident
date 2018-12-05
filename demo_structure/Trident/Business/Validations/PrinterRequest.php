<?php

namespace App\Trident\Business\Validations;

use Illuminate\Foundation\Http\FormRequest;
use Route;

class PrinterRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'id' => 'required',
            'string_parameter' => 'string',
            'integer_parameter' => 'integer',
        ];  //<-- praktika otan exw [] DEN exw validation.



        // return [
        //     'email' => 'required|email|unique:users',
        //     'name' => 'required|string|max:50',
        //     'password' => 'required'
        // ];
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            // 'id.required' => 'id is required!!',
            'string_parameter.required' => 'string_parameter is required!!',
            'string_parameter.string' => 'string_parameter must be string!!',
            'integer_parameter.integer' => 'integer_parameter must be integer!!',
        ];  //<-- praktika otan exw [] DEN exw validation.

        // return [
        //     'email.required' => 'Email is required!',
        //     'name.required' => 'Name is required!',
        //     'password.required' => 'Password is required!'
        // ];
    }

    /**
     * Add parameters to be validated (gia tn periptwsh tn GET parametrwn)
     * 
     * @return array
     */
    public function all($keys = null) 
    {
        $data = parent::all($keys);
        $data['id'] = $this->route('id');
        return $data;
    }

}