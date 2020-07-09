<?php

namespace App\Http\Requests\Option;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
    public function rules(Request $request)
    {   
        $data = [
            'base_currency'  => 'required|string|max:191'
        ];

        if (\Gate::allows('superAdmin')) $data['user_id'] = 'required|numeric|min:1';

        return $data;
    }
}
