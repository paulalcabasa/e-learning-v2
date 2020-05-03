<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainorValidation extends FormRequest
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
            'dealer_id' => 'required',
            'fname'     => 'required|string|max:50',
            'mname'     => 'nullable|string|max:20',
            'lname'     => 'required|string|max:20',
            'email'     => 'required|string|email|max:255|unique:trainors'
        ];
    }
}
