<?php

namespace App\Http\Requests\User;

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
            'fullname' => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users',
            'role'     => 'required|string|max:191',
            'status'   => 'required|string|max:191'
        ];
        if (\Gate::allows('superAdmin')) {
            if ($request->role=='hotel_receptionist') $data['assignTo'] = 'required|numeric|min:1';
        }
        return $data;
    }
}
