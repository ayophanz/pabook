<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    private $id;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('id');
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
            'name'           => 'required|string|max:191|unique:hotels,name,'.$this->id,
            'address'        => 'required|string',
            'city'           => 'required|string|max:191',
            'country'        => 'required|string|max:191',
            'phone_number'   => 'required|string|max:191',
            'email'          => 'required|string|email|max:191|unique:hotels,email,'.$this->id,
            'base_currency'  => 'required|string|max:191',
            'check_in'       => 'required',
            'check_out'      => 'required',
            'rooms_no'       => 'required'
            ];

        if ($request->changeCover) $data['image'] = 'required|image64:jpeg,jpg,png';    

        if (\Gate::allows('superAdmin')) $data['owner'] = 'required|numeric|min:1';

        return $data;
    }
}
