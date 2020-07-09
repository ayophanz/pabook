<?php

namespace App\Http\Requests\Book;

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
            'hotel' => 'required',
            'checkInD'  => 'required',
            'checkOutD' => 'required',
            'manyAdult' => 'required',
            'manyChild' => 'required',
            'manyRoom'  => 'required',
            'roomWithRoomType'  => 'required',
            'rooms_no'  => 'required|rooms_no_equal_room_name:'.$request->manyRoom.','.count($request->rooms_no)
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
            'hotel.required' => 'The hotel is required.',
            'checkInD.required' => 'The check-in or check-out date is required.',
            'checkOutD.required' => 'The check-in or check-out date is required.',
            'manyAdult.required' => 'The number of adult is required.',
            'manyChild.required' => 'The number of child is required.',
            'manyRoom.required' => 'The number of how many room is required.',
            'roomWithRoomType.required' => 'The room is required.',
            'rooms_no_equal_room_name' => 'The item on this field must match on "No. of room"'
        ];
    }
}
