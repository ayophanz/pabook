<?php

namespace App\Http\Requests\User;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    private $id;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = auth('api')->user()->id;
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
            'fullname' => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users,email,'.$this->id,
            'role'     => 'required|string|max:191',
            'status'   => 'required|string|max:191'
        ];

        if($request->changePass) {
            $data['password'] = 'required|string|min:6';
            $data['old_password'] = 'required|string|min:6|match_old_password:'.$request->old_password.','.auth('api')->user()->password;
        }
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
            'match_old_password' => 'The :attribute field doesn\'t match the current password.'
        ]; 
    }
}
