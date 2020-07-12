<?php

namespace App\Http\Requests\Room;

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
        return [
            'name' 	      => 'required|string|max:191|unique_name:rooms,name,room_type_id,'.$request->type.',0',
            'type'        => 'required|numeric|min:1',
            'price'       => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'no_of_room'  => 'required|numeric|min:0',
            'hotel'       => 'required|numeric|min:1',
            'image'       => 'required|image64:jpeg,jpg,png',
            'max_adult'   => 'required|numeric|min:1',
            'max_child'   => 'required|numeric|min:0'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'min' => 'The :attribute is required.',
            'unique_name' => 'The :attribute field is already exist in the same room type.'
        ]; 
    }
}
