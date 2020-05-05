<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleValidation extends FormRequest
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
            'module'      => 'required',
            'category_id' => 'required',
            'description' => 'max:48',
            'file_name'   => 'mimes:pdf'
        ];
    }

    public function messages()
    {
        return [];
    }
}
