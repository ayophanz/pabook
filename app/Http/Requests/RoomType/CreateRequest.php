<?php

namespace App\Http\Requests\RoomType;

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
            'name' 	=> 'required|string|max:191|unique_name:room_types,name,hotel_id,'.$request->hotel.',0',
            'hotel' => 'required|numeric|min:1'
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
            'unique_name' => 'The :attribute field is already exist in the same hotel name.'
        ]; 
    }
}
