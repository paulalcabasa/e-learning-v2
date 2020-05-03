<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ModuleScheduleValidation extends FormRequest
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
            'module_id'   => 'required|integer',
            'status'      => Rule::in(['waiting', 'on_going', 'completed']),
            'created_by'  => '' //--> Change this to [required|integer]
        ];
    }
}
