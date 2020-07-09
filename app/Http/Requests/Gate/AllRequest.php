<?php

namespace App\Http\Requests\Gate;

use Illuminate\Foundation\Http\FormRequest;

class AllRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!\Gate::allows('superAdmin') 
        && !\Gate::allows('hotelOwner') 
        && !\Gate::allows('hotelReceptionist'))
            return false;
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
            //
        ];
    }
}
