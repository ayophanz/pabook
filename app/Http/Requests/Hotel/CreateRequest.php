<?php

namespace App\Http\Requests\Hotel;

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
    public function rules()
    {   
        $data = [
            'name'           => 'required|string|max:191||unique:hotels',
            'address'        => 'required|string',
            'city'           => 'required|string|max:191',
            'country'        => 'required|string|max:191',
            'phone_number'   => 'required|string|max:191',
            'email'          => 'required|string|email|max:191|unique:hotels',
            'image'          => 'required|max:307200|image64:jpeg,jpg,png',
            'base_currency'  => 'required|string|max:191',
            'proofFile'      => 'required|max:5242880|image64:x-zip-compressed,zip',
            'check_in'       => 'required',
            'check_out'      => 'required',
            'rooms_no'       => 'required'
        ];

        if (\Gate::allows('superAdmin')) $data['owner'] = 'required|numeric|min:1';

        return $data;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'proofFile.image64' => 'The :attribute field is invalid, zip file only.'
        ];
    }
}
